<?php

declare(strict_types=1);

namespace App\Application\Actions\AppointmentRequests\Approve;

use App\Application\Actions\Action;
use App\Domain\Agendamentos\Services\AppointmentRequestService;
use App\Domain\Company\Repositories\CompanyRepository;
use Psr\Http\Message\ResponseInterface;

final class AppointmentRequestApproveAction extends Action
{
    public function __construct(
        private readonly AppointmentRequestService $service,
        private readonly CompanyRepository $companies
    ) {}

    public function action(): ResponseInterface
    {
        $requestId = (int) $this->resolveArg('id');
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData(['message' => 'Empresa não encontrada'], 404);
        }

        $data = (array) $this->request->getParsedBody();
        $appointment = $this->service->approve($requestId, $companyId, $data);
        if (!$appointment) {
            return $this->respondWithData(['message' => 'Solicitação não encontrada'], 404);
        }

        return $this->respondWithData($appointment);
    }
}
