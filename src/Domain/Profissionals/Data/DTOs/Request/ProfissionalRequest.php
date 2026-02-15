<?php 

namespace App\Domain\Profissionals\Data\DTOs\Request;

final class ProfissionalRequest {
    public function __construct(
        private int $companyId,
        private string $name,
        private string $email,
        private ?string $phone,
        private ?string $specialty,
    ) {}

    public function name(): string{
        return $this->name;
    }
    public function companyId(): int{
        return $this->companyId;
    }
    public function email(): string{
        return $this->email;
    }
    public function phone(): ?string{
        return $this->phone;    
    }
    public function specialty(): ?string{
        return $this->specialty;
    }

    public static function fromArray(array $data): self{
        return new self(
            companyId: (int) ($data['companyId'] ?? $data['company_id'] ?? 0),
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            specialty: $data['specialty'] ?? null,
        );
    }
}