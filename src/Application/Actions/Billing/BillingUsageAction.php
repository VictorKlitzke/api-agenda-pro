<?php

declare(strict_types=1);

namespace App\Application\Actions\Billing;

use App\Application\Actions\Action;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\CompanyPlan\Repositories\CompanyPlanRepository;
use App\Domain\Agendamentos\Repositories\AgendamentoRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

final class BillingUsageAction extends Action
{
    private const LIMITS = [
        'basic' => 200,
        'medium' => 800,
        'advanced' => 2000,
    ];

    public function __construct(
        LoggerInterface $logger,
        private readonly CompanyRepository $companies,
        private readonly CompanyPlanRepository $plans,
        private readonly AgendamentoRepository $appointments
    ) {
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        $userId = (int) $this->request->getAttribute('userId');
        
        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData(['message' => 'Empresa não encontrada'], 404);
        }

        $plan = $this->plans->findByCompanyId((int) $companyId);
        $planCode = strtolower(trim((string) ($plan['plan_code'] ?? 'basic')));
        $limit = self::LIMITS[$planCode] ?? 200;

        $now = new \DateTimeImmutable();
        $startOfMonth = $now->modify('first day of this month')->format('Y-m-d 00:00:00');
        $endOfMonth = $now->modify('last day of this month')->format('Y-m-d 23:59:59');

        $used = $this->appointments->countByCompanyAndPeriod((int) $companyId, $startOfMonth, $endOfMonth);

        return $this->respondWithData([
            'plan' => $planCode,
            'limit' => $limit,
            'used' => $used,
            'remaining' => max(0, $limit - $used),
            'percentage' => $limit > 0 ? round(($used / $limit) * 100, 2) : 0,
        ]);
    }
}
