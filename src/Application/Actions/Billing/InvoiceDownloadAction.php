<?php

declare(strict_types=1);

namespace App\Application\Actions\Billing;

use App\Application\Actions\Action;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\CompanyPlan\Repositories\CompanyPlanRepository;
use Psr\Log\LoggerInterface;
use Stripe\StripeClient;

final class InvoiceDownloadAction extends Action
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
            return $this->respondWithData(['message' => 'Unauthorized'], 401);
        }

        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData(['message' => 'Empresa não encontrada'], 404);
        }

        $plan = $this->plans->findByCompanyId((int) $companyId);
        $stripeCustomerId = $plan['stripe_customer_id'] ?? null;
        if (!$stripeCustomerId) {
            return $this->respondWithData(['message' => 'Stripe não configurado para esta empresa'], 404);
        }

        $ref = (string) ($this->args['id'] ?? '');
        if ($ref === '') {
            return $this->respondWithData(['message' => 'Invoice ref requerido'], 400);
        }

        // decode ref -> payload.hmac
        $parts = explode('.', $ref);
        if (count($parts) !== 2) {
            return $this->respondWithData(['message' => 'Ref inválida'], 400);
        }
        [$payloadB64, $sig] = $parts;
        $appSecret = (string) ($_ENV['APP_SECRET'] ?? '');
        if ($appSecret === '') {
            return $this->respondWithData(['message' => 'Server secret não configurado'], 500);
        }
        $expected = hash_hmac('sha256', $payloadB64, $appSecret);
        if (!hash_equals($expected, $sig)) {
            return $this->respondWithData(['message' => 'Ref inválida (assinatura)'], 403);
        }

        $invoiceId = base64_decode($payloadB64);
        if ($invoiceId === false || $invoiceId === '') {
            return $this->respondWithData(['message' => 'Ref inválida (payload)'], 400);
        }

        $secret = (string) ($_ENV['STRIPE_SECRET_KEY'] ?? '');
        if ($secret === '') {
            return $this->respondWithData(['message' => 'Stripe não configurado'], 500);
        }

        $stripe = new StripeClient($secret);

        try {
            $inv = $stripe->invoices->retrieve($invoiceId, []);
        } catch (\Throwable $e) {
            return $this->respondWithData(['message' => 'Fatura não encontrada'], 404);
        }

        // Verify ownership
        $invCustomer = (string) ($inv->customer ?? '');
        if ($invCustomer === '' || $invCustomer !== (string) $stripeCustomerId) {
            return $this->respondWithData(['message' => 'Não autorizado a acessar esta fatura'], 403);
        }

        $pdfUrl = $inv->invoice_pdf ?? null;
        if (!$pdfUrl) {
            return $this->respondWithData(['message' => 'PDF não disponível'], 404);
        }

        // fetch PDF server-side to avoid exposing Stripe public URL
        $opts = ["http" => ["method" => "GET", "header" => "User-Agent: agenda-pro\r\n"]];
        $context = stream_context_create($opts);
        $pdf = @file_get_contents($pdfUrl, false, $context);
        if ($pdf === false) {
            return $this->respondWithData(['message' => 'Falha ao baixar PDF da Stripe'], 500);
        }

        $stream = $this->response->getBody();
        $stream->write($pdf);

        return $this->response
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="invoice_' . $invoiceId . '.pdf"')
            ->withStatus(200);
    }
}
