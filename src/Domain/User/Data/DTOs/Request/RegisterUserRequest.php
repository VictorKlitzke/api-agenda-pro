<?php
declare(strict_types=1);

namespace App\Domain\User\Data\DTOs\Request;

use Doctrine\DBAL\Exception\InvalidArgumentException;

final class RegisterUserRequest
{
    public function __construct(
        private string $name,
        private string $email,
        private string $password,
        private string $cnpjcpf,
        private string $tipoConta,
        private string $telefone
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (strlen(trim($this->name)) < 3) {
            throw new InvalidArgumentException('Nome inválido.');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email inválido.');
        }

        if (strlen($this->password) < 6) {
            throw new InvalidArgumentException('Senha deve ter no mínimo 6 caracteres.');
        }
    }

    public static function fromArray(array $data): self
    {
        $role = $data['tipoConta'] ?? $data['tipo_conta'] ?? $data['role'] ?? '';

        return new self(
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['cnpjcpf'] ?? '',
            $role,
            $data['phone'] ?? ''
        );
    }

    // Getters explícitos
    public function name(): string { return $this->name; }
    public function email(): string { return $this->email; }
    public function password(): string { return $this->password; }
    public function cnpjcpf(): string { return $this->cnpjcpf; }
    public function tipoConta(): string { return $this->tipoConta; }
    public function telefone(): string { return $this->telefone; }
}
