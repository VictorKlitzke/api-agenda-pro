<?php
declare(strict_types=1);

namespace App\Infrastructure\Events;

use App\Domain\Shared\Events\EventInterface;

final class EventDispatcher
{
    /** @var array<string, callable[]> */
    private array $listeners = [];

    public function listen(string $eventClass, callable $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    public function dispatch(EventInterface $event): void
    {
        $eventClass = get_class($event);

        if (isset($this->listeners[$eventClass])) {
            foreach ($this->listeners[$eventClass] as $listener) {
                $listener($event);
            }
        }

        foreach (class_implements($event) as $interface) {
            if (isset($this->listeners[$interface])) {
                foreach ($this->listeners[$interface] as $listener) {
                    $listener($event);
                }
            }
        }
    }
}