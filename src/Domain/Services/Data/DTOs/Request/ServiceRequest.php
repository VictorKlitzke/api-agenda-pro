<?php 
namespace Domain\Services\Data\DTOs\Request;

final class ServiceRequest {

  public function __construct(
    private string $nameService,
    private string $observation,
    private float $price,
    private string $duration,
    private array $products
  ){
    $this->changeProducts(products: $products);
  }

  private function changeProducts(array $products): bool {
    if (count($products) === 0) {
      return false;
    }
    return true;
  }

  public static function fromArray(array $data): self {
    return new self(
      nameService: $data['nameService'],
      observation: $data['observation'],
      price: $data['price'],
      duration: $data['duration'],
      products: $data['products']
    );
  } 

}