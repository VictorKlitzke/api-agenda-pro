<?php
declare(strict_types=1);

namespace App\Domain\Shared\Interfaces;

interface MailerInterface
{
    public function send(string $to, string $subject, string $body, bool $isHtml = false): bool;
}
