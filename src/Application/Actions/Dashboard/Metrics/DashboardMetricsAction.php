<?php

declare(strict_types=1);

namespace App\Application\Actions\Dashboard\Metrics;

use App\Application\Actions\Action;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\Dashboard\Services\DashboardService;

final class DashboardMetricsAction extends Action
{
    public function __construct(
        private readonly DashboardService $service,
        private readonly CompanyRepository $companies
    ) {}

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData([
                'appointmentsToday' => 0,
                'newClientsToday' => 0,
                'lowStock' => [],
                'topServices' => [],
                'cancelRate' => ['total' => 0, 'canceled' => 0, 'rate' => 0],
            ]);
        }

        $metrics = $this->service->getMetrics($companyId);

        return $this->respondWithData($metrics);
    }
}
