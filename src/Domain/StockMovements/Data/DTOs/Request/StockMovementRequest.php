<?php

declare(strict_types=1);

namespace App\Domain\StockMovements\Data\DTOs\Request;

final class StockMovementRequest
{
    public function __construct(
        public readonly int $companyId,
        public readonly int $stockId,
        public readonly int $quantity,
        public readonly string $movementType,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            companyId: (int) ($data['companyId'] ?? 0),
            stockId: (int) ($data['stockId'] ?? 0),
            quantity: (int) ($data['quantity'] ?? 0),
            movementType: strtoupper((string) ($data['movementType'] ?? '')),
            notes: isset($data['notes']) ? (string) $data['notes'] : null,
        );
    }

    public function companyId(): int
    {
        return $this->companyId;
    }

    public function stockId(): int
    {
        return $this->stockId;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function movementType(): string
    {
        return $this->movementType;
    }

    public function notes(): ?string
    {
        return $this->notes;
    }
}
