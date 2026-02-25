<?php
declare(strict_types=1);

namespace App\Domain\User\Data\DTOs\Request;

use App\Infrastructure\Exceptions\ValidationException;

final class VerifyEmailCodeRequest
{
    public function __construct(
        private string $email,
        private string $verificationCode
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        $errors = [];

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido.';
        }

        if (!preg_match('/^\d{6}$/', $this->verificationCode)) {
            $errors['verification_code'] = 'Código de verificação inválido.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['email'] ?? ''),
            (string) ($data['verification_code'] ?? '')
        );
    }

    public function email(): string
    {
        return $this->email;
    }

    public function verificationCode(): string
    {
        return $this->verificationCode;
    }
}
