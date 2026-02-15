<?php 
namespace App\Domain\Services\Data\DTOs\Request;

final class ServiceRequest {

  public function __construct(
    private string $nameService,
    private string $observation,
    private float $price,
    private string $duration,
    private array $products,
    private int $companyId = 0
  ){
    $this->products = $products;
  }

  public static function fromArray(array $data): self {
    return new self(
      nameService: $data['name'] ?? $data['nameService'] ?? '',
      observation: $data['description'] ?? $data['observation'] ?? '',
      price: isset($data['price']) ? (float) $data['price'] : 0.0,
      duration: (string) ($data['duration'] ?? $data['durationMinutes'] ?? ''),
      products: $data['products'] ?? $data['productIds'] ?? [],
      companyId: (int) ($data['companyId'] ?? $data['company_id'] ?? 0)
    );
  }

  public function name(): string { return $this->nameService; }
  public function description(): string { return $this->observation; }
  public function price(): float { return $this->price; }
  public function duration(): string { return $this->duration; }
  public function products(): array { return $this->products; }
  public function companyId(): int { return $this->companyId; }

}