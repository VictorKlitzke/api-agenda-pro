<?php

declare(strict_types=1);

namespace App\Application\Actions\Billing;

use App\Application\Actions\Action;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\CompanyPlan\Repositories\CompanyPlanRepository;
use Psr\Log\LoggerInterface;

final class CompanyPlanStatusAction extends Action
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
            return $this->respondWithData(['plan' => null]);
        }

        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData(['plan' => null]);
        }

        $plan = $this->plans->findByCompanyId((int) $companyId);

        return $this->respondWithData([
            'plan' => $plan,
        ]);
    }
}
