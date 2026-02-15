<?php 

namespace App\Domain\Services\Services;

use App\Domain\Services\Entities\ServiceEntity;
use App\Domain\Services\Repositories\ServiceRepository;
use App\Domain\Services\Data\DTOs\Request\ServiceRequest;

final class ServiceServices {

    public function __construct(private readonly ServiceRepository $serviceRepository) {}

    public function register(ServiceRequest $request): ?ServiceEntity {
        $service = ServiceEntity::restore(
            id: 0,
            companyId: $request->companyId(),
            serviceName: $request->name(),
            price: $request->price(),
            description: $request->description(),
            duration: $request->duration(),
            active: true
        );

        return $this->serviceRepository->save(service: $service, products: $request->products());
    }

    public function update(ServiceRequest $request, int $id): bool {
        $service = ServiceEntity::restore(
            id: $id,
            companyId: $request->companyId(),
            serviceName: $request->name(),
            price: $request->price(),
            description: $request->description(),
            duration: $request->duration(),
            active: true
        );
        return $this->serviceRepository->update(service: $service, id: $id, products: $request->products());
    }

    public function findById(int $id): ?ServiceEntity {
        return $this->serviceRepository->findById(id: $id);
    }

    public function findAll(): array {
        return $this->serviceRepository->findAll();
    }

    public function findAllByCompanyId(int $companyId): array {
        return $this->serviceRepository->findAllByCompanyId(companyId: $companyId);
    }

    public function delete(int $id): bool {
        return $this->serviceRepository->delete(id: $id);
    }

    

}