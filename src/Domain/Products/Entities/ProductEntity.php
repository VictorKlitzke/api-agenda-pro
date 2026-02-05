<?php
declare(strict_types=1);

namespace App\Domain\Products\Entities;

use DateTimeImmutable;

final class ProductEntity
{
    private function __construct(
        private ?int $id,
        private string $name,
        private ?string $description,
        private ?float $price,
        private bool $active,
        private DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt
    ) {
        $this->changeName($name);
    }

    public static function create(string $name, ?string $description = null, ?float $price = null): self
    {
        return new self(
            id: null,
            name: $name,
            description: $description,
            price: $price,
            active: true,
            createdAt: new DateTimeImmutable(),
            updatedAt: null
        );
    }

    public static function restore(
        int $id,
        string $name,
        ?string $description,
        ?float $price,
        bool $active,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt
    ): self {
        return new self(
            id: $id,
            name: $name,
            description: $description,
            price: $price,
            active: $active,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }

    public function changeName(string $name): void
    {
        if (strlen(trim($name)) < 1) {
            throw new \InvalidArgumentException('Nome inválido.');
        }
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function changeDescription(?string $description): void
    {
        $this->description = $description;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function changePrice(?float $price): void
    {
        $this->price = $price;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function deactivate(): void
    {
        $this->active = false;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function activate(): void
    {
        $this->active = true;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function id(): ?int { return $this->id; }
    public function name(): string { return $this->name; }
    public function description(): ?string { return $this->description; }
    public function price(): ?float { return $this->price; }
    public function isActive(): bool { return $this->active; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
    public function updatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
}
