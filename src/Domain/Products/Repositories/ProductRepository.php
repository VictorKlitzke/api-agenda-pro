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

    public function save(ProductEntity $product): ProductEntity
    {
        $id = $this->connection->table('products')->insertGetId([
            'name' => $product->name(),
            'description' => $product->description(),
            'price' => $product->price(),
            'active' => $product->isActive() ? 1 : 0,
            'created_at' => $product->createdAt()->format('Y-m-d H:i:s'),
        ]);

        return $this->findById((int) $id);
    }

    public function update(ProductEntity $product): ProductEntity
    {
        $this->connection->table('products')
            ->where('id', $product->id())
            ->update([
                'name' => $product->name(),
                'description' => $product->description(),
                'price' => $product->price(),
                'active' => $product->isActive() ? 1 : 0,
                'updated_at' => $product->updatedAt()?->format('Y-m-d H:i:s'),
            ]);

        return $this->findById($product->id());
    }

    public function delete(int $id): bool
    {
        return $this->connection->table('products')
            ->where('id', $id)
            ->delete() > 0;
    }

    public function findAll(): array
    {
        $rows = $this->connection->table('products')->get();

        $result = [];
        foreach ($rows as $r) {
            $result[] = $this->mapToEntity((array) $r);
        }

        return $result;
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
            (bool) ($row['active'] ?? true),
            new \DateTimeImmutable($row['created_at']),
            isset($row['updated_at']) && $row['updated_at'] ? new \DateTimeImmutable($row['updated_at']) : null
        );
    }
}
