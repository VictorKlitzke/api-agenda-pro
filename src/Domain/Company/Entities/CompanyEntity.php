<?php

declare(strict_types=1);

namespace App\Domain\Company\Entities;

use DateTimeImmutable;

final class CompanyEntity
{
    private function __construct(
        private ?int $id,
        private int $userId,
        private string $name,
        private string $cnpj,
        private string $address,
        private string $city,
        private string $state,
        private bool $active,
        private DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt
    ) {
        $this->changeName($name);
    }

    public static function create(
        int $userId,
        string $name,
        string $cnpj,
        string $address,
        string $city,
        string $state
    ): self {
        return new self(
            id: null,
            userId: $userId,
            name: $name,
            cnpj: $cnpj,
            address: $address,
            city: $city,
            state: $state,
            active: true,
            createdAt: new DateTimeImmutable(),
            updatedAt: null
        );
    }

    public static function restore(
        int $id,
        int $userId,
        string $name,
        string $cnpj,
        string $address,
        string $city,
        string $state,
        bool $active,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt
    ): self {
        return new self(
            id: $id,
            userId: $userId,
            name: $name,
            cnpj: $cnpj,
            address: $address,
            city: $city,
            state: $state,
            active: $active,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }

    public function changeName(string $name): void
    {
        if (strlen(trim($name)) < 2) {
            throw new \InvalidArgumentException('Nome inválido.');
        }
        $this->name = $name;
    }

    public function changeAddress(string $address, string $city, string $state): void
    {
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function changeCnpj(string $cnpj): void
    {
        $this->cnpj = $cnpj;
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
    public function userId(): int { return $this->userId; }
    public function name(): string { return $this->name; }
    public function cnpj(): string { return $this->cnpj; }
    public function address(): string { return $this->address; }
    public function city(): string { return $this->city; }
    public function state(): string { return $this->state; }
    public function isActive(): bool { return $this->active; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
    public function updatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
}