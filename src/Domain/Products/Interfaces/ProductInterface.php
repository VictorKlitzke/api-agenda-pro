<?php
declare(strict_types=1);

namespace App\Domain\Products\Interfaces;

use App\Domain\Products\Entities\ProductEntity;

interface ProductInterface
{
    public function save(ProductEntity $product): ProductEntity;

    public function update(ProductEntity $product): ProductEntity;

    public function delete(int $id): bool;

    /** @return ProductEntity[] */
    public function findAll(): array;

    public function findById(int $id): ?ProductEntity;
}
