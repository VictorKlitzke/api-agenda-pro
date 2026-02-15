<?php 
namespace App\Domain\Company\Data\DTOs\Request;

final class UpdateCompanyRequest
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $cnpj,
        public readonly string $phone,
        public readonly string $address,
        public readonly string $city,
        public readonly string $state,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data["id"],
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            cnpj: $data['cnpj'] ?? '',
            phone: $data['phone'] ?? '',
            address: $data['address'] ?? '',
            city: $data['city'] ?? '',
            state: $data['state'] ?? '',
        );
    }

    public function id(): int
    {
        return $this->id;
    }
    public function name(): string
    {
        return $this->name;
    }
    public function email(): string
    {
        return $this->email;
    }
    public function cnpj(): string
    {
        return $this->cnpj;
    }
    public function phone(): string
    {
        return $this->phone;    
    }   
    public function address(): string
    {
        return $this->address;
    }
    public function city(): string
    {
        return $this->city;
    }
    public function state(): string
    {
        return $this->state;
    }
}