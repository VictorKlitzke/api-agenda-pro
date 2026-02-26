<?php

declare(strict_types=1);

namespace App\Infrastructure\Whatsapp;

use App\Domain\Shared\Interfaces\WhatsappNotifierInterface;

final class NullWhatsappNotifier implements WhatsappNotifierInterface
{
    public function sendText(string $phone, string $message, array $metadata = []): bool
    {
        return false;
    }
}
