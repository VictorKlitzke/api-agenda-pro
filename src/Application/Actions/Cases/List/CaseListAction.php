<?php

declare(strict_types=1);

namespace App\Application\Actions\Cases\List;

use App\Application\Actions\Action;
use App\Domain\Cases\Services\CaseService;
use App\Domain\Company\Repositories\CompanyRepository;
use Psr\Http\Message\ResponseInterface;

final class CaseListAction extends Action
{
    public function __construct(
        private readonly CaseService $service,
        private readonly CompanyRepository $companies
    ) {}

    public function action(): ResponseInterface
    {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData([]);
        }

        $list = $this->service->findAllByCompanyId($companyId);

        return $this->respondWithData($list);
    }
}
