<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Services;

use App\Domain\Dashboard\Repositories\DashboardRepository;

final class DashboardService
{
    public function __construct(private DashboardRepository $repository) {}

    public function getMetrics(int $companyId, int $lowStockThreshold = 5, int $topDays = 30): array
    {
        $today = (new \DateTimeImmutable())->format('Y-m-d');

        return [
            'appointmentsToday' => $this->repository->countAppointmentsToday($companyId, $today),
            'newClientsToday' => $this->repository->countNewClientsToday($companyId, $today),
            'lowStock' => $this->repository->getLowStockProducts($companyId, $lowStockThreshold),
            'topServices' => $this->repository->getTopServices($companyId, $topDays),
            'cancelRate' => $this->repository->getCancellationRate($companyId, $topDays),
        ];
    }
}
