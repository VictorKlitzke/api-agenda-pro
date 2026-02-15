<?php

declare(strict_types=1);

namespace App\Application\Actions\AppointmentRequests\List;

use App\Application\Actions\Action;
use App\Domain\Agendamentos\Services\AppointmentRequestService;
use App\Domain\Company\Repositories\CompanyRepository;
use Psr\Http\Message\ResponseInterface;

final class AppointmentRequestListAction extends Action
{
    public function __construct(
        private readonly AppointmentRequestService $service,
        private readonly CompanyRepository $companies
    ) {}

    public function action(): ResponseInterface
    {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData([]);
        }

        $params = $this->request->getQueryParams();
        $status = isset($params['status']) ? (string) $params['status'] : null;

        $list = $this->service->listByCompanyId($companyId, $status);

        return $this->respondWithData($list);
    }
}
