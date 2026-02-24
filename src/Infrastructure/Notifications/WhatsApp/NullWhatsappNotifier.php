<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications\WhatsApp;

use App\Domain\Shared\Interfaces\WhatsappNotifierInterface;

final class NullWhatsappNotifier implements WhatsappNotifierInterface
{
    public function sendText(string $phone, string $message): bool
    {
        return false;
    }
}
