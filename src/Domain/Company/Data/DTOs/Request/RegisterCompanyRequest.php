<?php 
namespace App\Domain\Company\Data\DTOs\Request;


final class RegisterCompanyRequest
{
    public function __construct(
        private int $userId,
        private string $name,
        private string $cnpj,
        private string $address,
        private string $city,
        private string $state
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userId: (int) ($data['userId'] ?? 0),
            name: $data['name'] ?? '',
            cnpj: $data['cnpj'] ?? '',
            address: $data['address'] ?? '',
            city: $data['city'] ?? '',
            state: $data['state'] ?? ''
        );
    }

    public function userId(): int { return $this->userId; }
    public function name(): string { return $this->name; }
    public function cnpj(): string { return $this->cnpj; }
    public function address(): string { return $this->address; }
    public function city(): string { return $this->city; }
    public function state(): string { return $this->state; }
}