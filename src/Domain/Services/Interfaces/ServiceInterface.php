<?php 
namespace App\Domain\Services\Interfaces;

use App\Domain\Services\Entities\ServiceEntity;

interface ServiceInterface {

    public function save(ServiceEntity $service): ServiceEntity;

    public function update(ServiceEntity $service): bool;

    public function delete(int $id): bool;

    /** @return ServiceEntity[] */
    public function findAll(): array;

    public function findById(int $id): ?ServiceEntity;

}