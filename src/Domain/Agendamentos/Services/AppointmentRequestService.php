<?php

declare(strict_types=1);

namespace App\Domain\Agendamentos\Services;

use App\Domain\Agendamentos\Data\DTOs\Request\AgendamentoRequest;
use App\Domain\Agendamentos\Repositories\AppointmentRequestRepository;

final class AppointmentRequestService
{
    public function __construct(
        private readonly AppointmentRequestRepository $repository,
        private readonly AgendamentoService $agendamentoService
    ) {}

    public function create(array $data): array
    {
        return $this->repository->create($data);
    }

    public function listByCompanyId(int $companyId, ?string $status = null): array
    {
        return $this->repository->findByCompanyId($companyId, $status);
    }

    public function approve(int $requestId, int $companyId, array $payload): array
    {
        $request = $this->repository->findById($requestId);
        if (!$request || (int) ($request['company_id'] ?? 0) !== $companyId) {
            return [];
        }

        $requestDto = AgendamentoRequest::fromArray(array_merge($payload, [
            'companyId' => $companyId,
        ]));

        $appointment = $this->agendamentoService->register($requestDto);
        $appointmentId = (int) ($appointment['id'] ?? 0);

        $this->repository->updateStatus($requestId, 'APPROVED', $appointmentId ?: null);

        return $appointment;
    }

    public function reject(int $requestId, int $companyId): bool
    {
        $request = $this->repository->findById($requestId);
        if (!$request || (int) ($request['company_id'] ?? 0) !== $companyId) {
            return false;
        }

        return $this->repository->updateStatus($requestId, 'REJECTED');
    }
}
