<?php

declare(strict_types=1);

namespace App\Domain\StockMovements\Repositories;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\StockMovements\Data\DTOs\Request\StockMovementRequest;
use Illuminate\Database\Connection;

final class StockMovementRepository
{
    public function __construct(protected Connection $connection)
    {
    }

    public function findByCompanyId(int $companyId): array
    {
        $rows = $this->connection->table('stock_movements')
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        return array_map(fn($row) => $this->mapToArray((array) $row), $rows);
    }

    public function findByStockId(int $stockId): array
    {
        $rows = $this->connection->table('stock_movements')
            ->where('stock_id', $stockId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        return array_map(fn($row) => $this->mapToArray((array) $row), $rows);
    }

    public function create(StockMovementRequest $request): array
    {
        return $this->connection->transaction(function () use ($request) {
            $product = $this->connection->table('products')
                ->where('id', $request->stockId())
                ->first();

            $current = (int) ($product->quantity ?? 0);
            $delta = $request->movementType() === 'IN' ? $request->quantity() : -$request->quantity();

            $createdAt = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

            $id = $this->connection->table('stock_movements')->insertGetId([
                'company_id' => $request->companyId(),
                'stock_id' => $request->stockId(),
                'quantity' => $request->quantity(),
                'movement_type' => $request->movementType(),
                'notes' => $request->notes(),
                'created_at' => $createdAt,
            ]);

            $this->connection->table('products')
                ->where('id', $request->stockId())
                ->update([
                    'quantity' => $current + $delta,
                    'updated_at' => $createdAt,
                ]);

            $row = $this->connection->table('stock_movements')->where('id', $id)->first();

            return $row ? $this->mapToArray((array) $row) : [
                'id' => $id,
                'companyId' => $request->companyId(),
                'stockId' => $request->stockId(),
                'quantity' => $request->quantity(),
                'movementType' => $request->movementType(),
                'notes' => $request->notes(),
                'createdAt' => $createdAt,
            ];
        });
    }

    public function delete(int $id): bool
    {
        return $this->connection->transaction(function () use ($id) {
            $row = $this->connection->table('stock_movements')->where('id', $id)->first();
            if (!$row) {
                throw new DomainRecordNotFoundException('Movimento não encontrado');
            }

            $movement = (array) $row;
            $product = $this->connection->table('products')
                ->where('id', (int) $movement['stock_id'])
                ->first();


            $current = (int) ($product->quantity ?? 0);
            $qty = (int) $movement['quantity'];
            $delta = $movement['movement_type'] === 'IN' ? -$qty : $qty;
            $newQty = $current + $delta;

            $this->connection->table('products')
                ->where('id', (int) $movement['stock_id'])
                ->update([
                    'quantity' => $newQty,
                    'updated_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                ]);

            return $this->connection->table('stock_movements')
                ->where('id', $id)
                ->delete() > 0;
        });
    }

    private function mapToArray(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'companyId' => (int) $row['company_id'],
            'stockId' => (int) $row['stock_id'],
            'quantity' => (int) $row['quantity'],
            'movementType' => (string) $row['movement_type'],
            'notes' => $row['notes'] ?? null,
            'createdAt' => $row['created_at'],
        ];
    }
}
