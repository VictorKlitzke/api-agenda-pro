<?php 
namespace App\Domain\Profissionals\Entities;

final class ProfissionalEntity {
    public function __construct(
        private ?int $id,
        private int $companyId,
        private string $name,
        private string $email,
        private ?string $phone,
        private ?string $specialty,
        private \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $companyId,
        string $name,
        string $email,
        ?string $phone = null,
        ?string $specialty = null,
        ?string $status = null,
    ): self {
        return new self(
            id: null,
            companyId: $companyId,
            name: $name,
            email: $email,
            phone: $phone,
            specialty: $specialty,
            createdAt: new \DateTimeImmutable(),
            updatedAt: null,
        );
    }

    public static function restore(
        int $id,
        int $companyId,
        string $name,
        string $email,
        ?string $phone,
        ?string $specialty,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            id: $id,
            companyId: $companyId,
            name: $name,
            email: $email,
            phone: $phone,
            specialty: $specialty,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function getId(): ?int {
        return $this->id;
    }
    public function getCompanyId(): int {
        return $this->companyId;
    }
    public function getName(): string {
        return $this->name;
    }
    public function getEmail(): string {
        return $this->email;
    }
    public function getPhone(): ?string {
        return $this->phone;
    }
    public function getSpecialty(): ?string {
        return $this->specialty;
    }
    public function getCreatedAt(): \DateTimeImmutable {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?\DateTimeImmutable {
        return $this->updatedAt;
    }
}