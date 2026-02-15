<?php

declare(strict_types=1);

namespace App\Application\Actions\AppointmentRequests\Reject;

use App\Application\Actions\Action;
use App\Domain\Agendamentos\Services\AppointmentRequestService;
use App\Domain\Company\Repositories\CompanyRepository;
use Psr\Http\Message\ResponseInterface;

final class AppointmentRequestRejectAction extends Action
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

        $updated = $this->service->reject($requestId, $companyId);
        if (!$updated) {
            return $this->respondWithData(['message' => 'Solicitação não encontrada'], 404);
        }

        return $this->respondWithData(['status' => 'REJECTED']);
    }
}
