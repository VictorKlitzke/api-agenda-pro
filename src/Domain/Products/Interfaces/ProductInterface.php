<?php
declare(strict_types=1);

namespace App\Domain\Products\Interfaces;

use App\Domain\Products\Entities\ProductEntity;
    
interface ProductInterface
{
    public function save(ProductEntity $product): bool;

    public function update(ProductEntity $product, int $id): ProductEntity;

    public function delete(int $id): bool;

    public function findAll(): array;

    public function findAllByCompanyId(int $companyId): array;

    public function findById(int $id): ?ProductEntity;
}
