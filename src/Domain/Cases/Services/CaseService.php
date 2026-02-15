<?php

declare(strict_types=1);

namespace App\Domain\Cases\Services;

use App\Domain\Cases\Data\DTOs\Request\CaseRequest;
use App\Domain\Cases\Entities\CaseEntity;
use App\Domain\Cases\Repositories\CaseRepository;

final class CaseService
{
    public function __construct(private readonly CaseRepository $repository) {}

    public function register(CaseRequest $request): array
    {
        $case = CaseEntity::create(
            companyId: $request->companyId(),
            clientId: $request->clientId(),
            professionalId: $request->professionalId(),
            title: $request->title(),
            caseNumber: $request->caseNumber(),
            area: $request->area(),
            status: $request->status(),
            priority: $request->priority(),
            notes: $request->notes(),
        );

        return $this->repository->save($case);
    }

    public function update(int $id, CaseRequest $request): bool
    {
        $case = CaseEntity::restore(
            id: $id,
            companyId: $request->companyId(),
            clientId: $request->clientId(),
            professionalId: $request->professionalId(),
            title: $request->title(),
            caseNumber: $request->caseNumber(),
            area: $request->area(),
            status: $request->status(),
            priority: $request->priority(),
            notes: $request->notes(),
        );

        return $this->repository->update($case, $id);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function findAllByCompanyId(int $companyId): array
    {
        return $this->repository->findAllByCompanyId($companyId);
    }

    public function findById(int $id): ?array
    {
        return $this->repository->findById($id);
    }
}
