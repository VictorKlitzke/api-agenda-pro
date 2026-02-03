<?php
namespace App\Domain\Shared\Events;

interface EmailEventInterface extends EventInterface
{
    public function getTo(): string;
    public function getSubject(): string;
    public function getBody(): string;
    public function isHtml(): bool;
}