<?php

declare(strict_types=1);

namespace App\Domain\CompanyPlan\Services;

use App\Domain\CompanyPlan\Repositories\CompanyPlanRepository;

final class CompanyPlanService
{
    public function __construct(private CompanyPlanRepository $repository) {}

    public function upsert(int $companyId, array $data): array
    {
        return $this->repository->upsert($companyId, $data);
    }
}
