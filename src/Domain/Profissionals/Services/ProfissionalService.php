<?php 

namespace App\Domain\Profissionals\Services;

use App\Domain\Profissionals\Data\DTOs\Request\ProfissionalRequest;
use App\Domain\Profissionals\Entities\ProfissionalEntity;
use App\Domain\Profissionals\Repositories\ProfissionalRepository;

final class ProfissionalService {
    
    public function __construct(private readonly ProfissionalRepository $repository){}


    public function register(ProfissionalRequest $request): bool {
        $profissional = ProfissionalEntity::create(
            companyId: $request->companyId(),
            name: $request->name(),
            email: $request->email(),
            phone: $request->phone(),
            specialty: $request->specialty(),
        );
        return $this->repository->register(profissional: $profissional);
    }

    public function update(int $id, ProfissionalRequest $request): bool {
        $existing = $this->repository->find(id: $id);
        if (!$existing) return false;
        
        $profissional = ProfissionalEntity::restore(
            id: $id,
            companyId: $request->companyId(),
            name: $request->name(),
            email: $request->email(),
            phone: $request->phone(),
            specialty: $request->specialty(),
            createdAt: $existing->getCreatedAt(),
            updatedAt: new \DateTimeImmutable(),
        );
        return $this->repository->update(profissional: $profissional);
    }

    public function delete(int $id): bool {
        $existing = $this->repository->find(id: $id);
        if (!$existing) {
            return false;
        }
        $profissional = ProfissionalEntity::restore(
            id: $id,
            companyId: $existing->getCompanyId(),
            name: $existing->getName(),
            email: $existing->getEmail(),
            phone: $existing->getPhone(),
            specialty: $existing->getSpecialty(),
            createdAt: $existing->getCreatedAt(),
            updatedAt: $existing->getUpdatedAt(),
        );
        return $this->repository->delete(profissional: $profissional);
    }

    public function findAll(): array {
        return $this->repository->findAll();
    }

    public function findAllByCompanyId(int $companyId): array {
        return $this->repository->findAllByCompanyId($companyId);
    }

    public function find(int $id): ?ProfissionalEntity {
        return $this->repository->find(id: $id);
    }

    public function active(int $id, string $status): bool {
        return $this->repository->active(id: $id, status: $status);
    }

}