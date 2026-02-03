<?php
declare(strict_types=1);

namespace App\Domain\User\Events;

use App\Domain\Shared\Events\EmailEventInterface;

final class UserRegisteredEvent implements EmailEventInterface
{
    public function __construct(
        public readonly ?int $userId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $verificationCode
    ) {
    }

    public function getName(): string
    {
        return 'user.registered';
    }

    public function getTo(): string
    {
        return $this->email;
    }

    public function getSubject(): string
    {
        return 'Código de verificação - Agenda Pro';
    }

    public function getBody(): string
    {
        return sprintf(
            '<h2>Olá %s!</h2>
            <p>Bem-vindo ao Agenda Pro.</p>
            <p>Seu código de verificação é: <strong style="font-size: 24px;">%s</strong></p>',
            htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($this->verificationCode, ENT_QUOTES, 'UTF-8')
        );
    }

    public function isHtml(): bool
    {
        return true;
    }
}