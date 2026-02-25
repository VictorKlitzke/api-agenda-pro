<?php
declare(strict_types=1);

namespace App\Infrastructure\Mail;

use App\Domain\Shared\Interfaces\MailerInterface;

final class NullMailer implements MailerInterface
{
    public function send(string $to, string $subject, string $body, bool $isHtml = false): bool
    {
        return true;
    }
}
