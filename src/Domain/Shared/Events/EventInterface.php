<?php
declare(strict_types=1);

namespace App\Domain\Shared\Events;

interface EventInterface
{
    public function getName(): string;
}