<?php
namespace App\Domain\Services\Entities;


class ServiceEntity
{
  public function __construct(
    private int $id,
    private int $companyId,
    private string $serviceName,
    private float $price,
    private ?string $description,
    private string $duration,
    private bool $active,
  ) {
  }

  public static function restore(
    int $id,
    int $companyId,
    string $serviceName,
    float $price,
    ?string $description,
    string $duration,
    bool $active
  ): self {
    return new self(
      id: $id,
      companyId: $companyId,
      serviceName: $serviceName,
      price: $price,
      description: $description,
      duration: $duration,
      active: $active
    );
  }

  public function save(
    int $companyId,
    string $serviceName,
    float $price,
    ?string $description,
    string $duration,
    bool $active
  ) {
    return new self(
      id: 0,
      companyId: $companyId,
      serviceName: $serviceName,
      price: $price,
      description: $description,
      duration: $duration,
      active: $active
    );
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getServiceName(): string
  {
    return $this->serviceName;
  }

  public function getPrice(): float
  {
    return $this->price;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function getDuration(): string
  {
    return $this->duration;
  }

  public function isActive(): bool
  {
    return $this->active;
  }

  public function getCompanyId(): int
  {
    return $this->companyId;
  }
}