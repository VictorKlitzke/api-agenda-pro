<?php
declare(strict_types=1);

namespace App\Domain\Products\Repositories;

use App\Domain\Products\Entities\ProductEntity;
use App\Domain\Products\Interfaces\ProductInterface;
use Illuminate\Database\Connection;

class ProductRepository implements ProductInterface
{
    public function __construct(protected Connection $connection)
    {
    }

    public function save(ProductEntity $product): bool 
    {
        $id = $this->connection->table('products')->insert([
            'name' => $product->name(),
            'description' => $product->description(),
            'price' => $product->price(),
            'quantity' => $product->quantity(),
            'company_id' => $product->companyId(),
            'active' => $product->isActive() ? 1 : 0,
            'created_at' => $product->createdAt()->format('Y-m-d H:i:s'),
        ]);

        return true;
    }

    public function update(ProductEntity $product, int $id): ProductEntity
    {
        $this->connection->table('products')
            ->where('id', $id)
            ->update([
                'name' => $product->name(),
                'description' => $product->description(),
                'price' => $product->price(),
                'quantity' => $product->quantity(),
                'company_id' => $product->companyId(),
                'active' => $product->isActive() ? 1 : 0,
                'updated_at' => $product->updatedAt()?->format('Y-m-d H:i:s'),
            ]);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        return $this->connection->table('products')
            ->where('id', $id)
            ->delete() > 0;
    }

    public function findAll(): array
    {
        $result = $this->connection->table('products')->get()->toArray();

        return $result;
    }

    public function findAllByCompanyId(int $companyId): array
    {
        return $this->connection->table('products')
            ->where('company_id', $companyId)
            ->get()
            ->toArray();
    }

    public function findById(int $id): ?ProductEntity
    {
        $row = $this->connection->table('products')->where('id', $id)->first();

        return $row ? $this->mapToEntity((array) $row) : null;
    }

    private function mapToEntity(array $row): ProductEntity
    {
        return ProductEntity::restore(
            (int) $row['id'],
            (string) $row['name'],
            isset($row['description']) ? (string) $row['description'] : null,
            isset($row['price']) && $row['price'] !== null ? (float) $row['price'] : null,
            isset($row['quantity']) && $row['quantity'] !== null ? (int) $row['quantity'] : null,
            isset($row['company_id']) && $row['company_id'] !== null ? (int) $row['company_id'] : null,
            (bool) ($row['active'] ?? true),
            new \DateTimeImmutable($row['created_at']),
            isset($row['updated_at']) && $row['updated_at'] ? new \DateTimeImmutable($row['updated_at']) : null
        );
    }
}
