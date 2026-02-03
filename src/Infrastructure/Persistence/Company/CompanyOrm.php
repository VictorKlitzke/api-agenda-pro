<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Company;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'companies')]
class CompanyOrm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'user_id', type: 'integer')]
    private int $userId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $cnpj = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: 'string', length: 120, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $active = true;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(int $userId, string $name, ?string $cnpj, ?string $address, ?string $city, ?string $state)
    {
        $this->userId = $userId;
        $this->name = $name;
        $this->cnpj = $cnpj;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getName(): string { return $this->name; }
    public function getCnpj(): ?string { return $this->cnpj; }
    public function getAddress(): ?string { return $this->address; }
    public function getCity(): ?string { return $this->city; }
    public function getState(): ?string { return $this->state; }
    public function isActive(): bool { return $this->active; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    public function setName(string $name): void { $this->name = $name; }
    public function setCnpj(?string $cnpj): void { $this->cnpj = $cnpj; }
    public function setAddress(?string $address): void { $this->address = $address; }
    public function setCity(?string $city): void { $this->city = $city; }
    public function setState(?string $state): void { $this->state = $state; }
    public function setActive(bool $active): void { $this->active = $active; }
    public function setUpdatedAt(?\DateTimeImmutable $dt): void { $this->updatedAt = $dt; }
}