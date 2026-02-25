<?php
declare(strict_types=1);

namespace App\Domain\User\Data\DTOs\Request;

use App\Infrastructure\Exceptions\ValidationException;

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
        $errors = [];

        if (strlen(trim($this->name)) < 3) {
            $errors['name'] = 'Nome inválido.';
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido.';
        }

        if (strlen($this->password) < 6) {
            $errors['password'] = 'Senha deve ter no mínimo 6 caracteres.';
        }

        if (strlen(trim($this->cnpjcpf)) < 11) {
            $errors['cnpjcpf'] = 'Documento inválido.';
        }

        if (strlen(trim($this->telefone)) < 10) {
            $errors['phone'] = 'Telefone inválido.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
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
