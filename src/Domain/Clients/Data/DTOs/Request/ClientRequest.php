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
        $this->changePhone($phone);
    }

    public function changePhone(string $phone): void {
        if (strlen($phone) < 11) throw new CustomException("Contato deve ter 11 dígitos", 400);
    }

    public static function fromArray(array $data): self
    {
        $rawPhone = $data['phone'] ?? '';
        $phone = preg_replace('/\D+/', '', (string) $rawPhone);

        return new self(
            name: (string) ($data['name'] ?? ''),
            phone: $phone,
            origem: $data['origem'] ?? null,
        );
    }
}