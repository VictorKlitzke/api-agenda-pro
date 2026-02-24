<?php

declare(strict_types=1);

namespace App\Domain\Shared\Interfaces;

interface WhatsappNotifierInterface
{
    public function sendText(string $phone, string $message): bool;
}
