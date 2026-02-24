<?php

declare(strict_types=1);

namespace App\Application\Actions\Billing;

use App\Application\Actions\Action;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\CompanyPlan\Repositories\CompanyPlanRepository;
use Psr\Log\LoggerInterface;
use Stripe\StripeClient;

final class InvoicesListAction extends Action
{
    public function __construct(
        LoggerInterface $logger,
        private CompanyRepository $companies,
        private CompanyPlanRepository $plans
    ) {
        parent::__construct($logger);
    }

    protected function action(): \Psr\Http\Message\ResponseInterface
    {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        if ($userId <= 0) {
            return $this->respondWithData([]);
        }

        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData([]);
        }

        $plan = $this->plans->findByCompanyId((int) $companyId);
        $stripeCustomerId = $plan['stripe_customer_id'] ?? null;
        if (!$stripeCustomerId) {
            return $this->respondWithData([]);
        }

        $secret = (string) ($_ENV['STRIPE_SECRET_KEY'] ?? '');
        if ($secret === '') {
            return $this->respondWithData(['message' => 'Stripe não configurado'], 500);
        }

        $stripe = new StripeClient($secret);

        // Optional month filter: YYYY-MM
        $query = $this->request->getQueryParams();
        $month = isset($query['month']) ? trim((string) $query['month']) : '';

        $params = ['customer' => $stripeCustomerId, 'limit' => 100];

        if ($month !== '') {
            try {
                $start = new \DateTimeImmutable($month . '-01 00:00:00');
                $end = (new \DateTimeImmutable($month . '-01 00:00:00'))->modify('last day of this month')->setTime(23, 59, 59);
                $params['created'] = [
                    'gte' => $start->getTimestamp(),
                    'lte' => $end->getTimestamp(),
                ];
            } catch (\Throwable $e) {
                // ignore invalid month format and return all invoices
            }
        }

        try {
            $res = $stripe->invoices->all($params);
            $list = [];
            $appSecret = (string) ($_ENV['APP_SECRET'] ?? '');
            foreach ($res->data ?? [] as $inv) {
                $invoiceId = $inv->id ?? '';
                // create opaque ref: base64(invoiceId).hmac
                $payload = base64_encode($invoiceId);
                $hmac = $appSecret !== '' ? hash_hmac('sha256', $payload, $appSecret) : '';
                $ref = $payload . '.' . $hmac;

                // friendly display id: use Stripe's number when available, otherwise short hash
                $displayId = $inv->number ?? ('INV-' . strtoupper(substr($hmac, 0, 8)));

                $due = null;
                if (isset($inv->due_date) && $inv->due_date) {
                    $due = date('Y-m-d', $inv->due_date);
                } elseif (isset($inv->created) && $inv->created) {
                    $due = date('Y-m-d', $inv->created);
                }

                $list[] = [
                    'ref' => $ref,
                    'display_id' => $displayId,
                    'amount' => $inv->amount_due ?? ($inv->total ?? null),
                    'currency' => $inv->currency ?? null,
                    'status' => $inv->status ?? null,
                    'due_date' => $due,
                    'description' => $inv->description ?? null,
                ];
            }

            return $this->respondWithData($list);
        } catch (\Throwable $e) {
            return $this->respondWithData(['message' => 'Erro ao consultar Stripe'], 500);
        }
    }
}
