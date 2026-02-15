<?php

declare(strict_types=1);

namespace App\Domain\StockMovements\Services;

use App\Domain\StockMovements\Data\DTOs\Request\StockMovementRequest;
use App\Domain\StockMovements\Repositories\StockMovementRepository;

final class StockMovementService
{
    public function __construct(private readonly StockMovementRepository $repository)
    {
    }

    public function create(StockMovementRequest $request): array
    {
        $type = $request->movementType();
        if (!in_array($type, ['IN', 'OUT'], true)) {
            throw new \InvalidArgumentException('Tipo de movimento inválido');
        }

        if ($request->quantity() <= 0) {
            throw new \InvalidArgumentException('Quantidade inválida');
        }

        return $this->repository->create($request);
    }

    public function findByCompanyId(int $companyId): array
    {
        return $this->repository->findByCompanyId($companyId);
    }

    public function findByStockId(int $stockId): array
    {
        return $this->repository->findByStockId($stockId);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
