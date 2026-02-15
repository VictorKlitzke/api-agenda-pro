<?php 
namespace App\Domain\Products\Services;

use App\Domain\Products\Data\DTOs\Request\PreductRequest;
use App\Domain\Products\Entities\ProductEntity;
use App\Domain\Products\Repositories\ProductRepository;

class ProductService {
    public function __construct(private readonly ProductRepository $repository){}

    public function register(PreductRequest $preductRequest): bool {

        $product = ProductEntity::create(
            name: $preductRequest->name(),
            description: $preductRequest->description(),
            price: $preductRequest->price(),
            quantity: $preductRequest->quantity(),
            companyId: $preductRequest->companyId(),
        );
        return $this->repository->save(product: $product);
    }

    public function findById(int $id): ?ProductEntity {
        return $this->repository->findById(id: $id);
    }

    public function findAll(): array {
        return $this->repository->findAll();
    }

    public function findAllByCompanyId(int $companyId): array {
        return $this->repository->findAllByCompanyId($companyId);
    }

    public function update(PreductRequest $preductRequest, int $id): ?ProductEntity {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return null;
        }

        $product = ProductEntity::restore(
            id: $id,
            name: $preductRequest->name(),
            description: $preductRequest->description(),
            price: $preductRequest->price(),
            active: $existing->isActive(),
            quantity: $preductRequest->quantity(),
            companyId: $preductRequest->companyId(),
            createdAt: $existing->createdAt(),
            updatedAt: new \DateTimeImmutable()
        );

        return $this->repository->update(product: $product, id: $id);
    }   

    public function delete(int $id): bool {
        return $this->repository->delete(id: $id);
    }
}