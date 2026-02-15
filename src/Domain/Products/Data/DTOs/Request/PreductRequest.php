<?php 

namespace App\Domain\Products\Data\DTOs\Request;

final class PreductRequest {
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly ?string $description,
        public readonly int $quantity,
        public readonly int $companyId,
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            name: $data['name'],
            price: (float) $data['price'],
            description: $data['description'] ?? null,
            quantity: (int) ($data['quantity'] ?? 0),
            companyId: (int) $data['companyId'],
        );
    }

    public function name(): string {
        return $this->name;
    }
    public function price(): float{
        return $this->price;
    }
    public function description(): ?string {
        return $this->description;
    }
    public function quantity(): int {
        return $this->quantity;
    }
    public function companyId(): int {
        return $this->companyId;
    }
}

