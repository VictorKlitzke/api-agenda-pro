<?php 
namespace App\Domain\Services\Interfaces;

use App\Domain\Services\Entities\ServiceEntity;

interface ServiceInterface {

    public function save(ServiceEntity $service, array $products = []): ServiceEntity;

    public function update(ServiceEntity $service, int $id, array $products = []): bool;

    public function delete(int $id): bool;

    /** @return ServiceEntity[] */
    public function findAll(): array;

    /** @return array */
    public function findAllByCompanyId(int $companyId): array;

    public function findById(int $id): ?ServiceEntity;

}