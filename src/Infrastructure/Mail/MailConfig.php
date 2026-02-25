<?php 

namespace App\Infrastructure\Mail;

final class MailConfig
{
    public function __construct(
        public readonly bool $enabled,
        public readonly string $from,
        public readonly string $host,
        public readonly int $port,
        public readonly ?string $user,
        public readonly ?string $password,
        public readonly string $secure,
    ) {}
}
