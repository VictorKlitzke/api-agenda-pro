<?php 

namespace App\Domain\Clients\Data\DTOs\Request;

use App\Infrastructure\Exceptions\CustomException;


final class ClientRequest 
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly ?string $origem
    ) {
        $this->changePhone(phone: $phone);
    }

    public function changePhone(string $phone): void {
        if (strlen($this->phone) < 11) {
            throw new CustomException("Contanto tem que ter 11 caracteres");
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            phone: $data['phone'] ?? '',
            origem: $data['origem'] ?? null,
        );
    }
}