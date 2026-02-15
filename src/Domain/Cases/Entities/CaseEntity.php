<?php

declare(strict_types=1);

namespace App\Domain\Cases\Entities;

final class CaseEntity
{
    public function __construct(
        private int $id,
        private int $companyId,
        private ?int $clientId,
        private ?int $professionalId,
        private string $title,
        private ?string $caseNumber,
        private ?string $area,
        private string $status,
        private string $priority,
        private ?string $notes
    ) {}

    public static function create(
        int $companyId,
        ?int $clientId,
        ?int $professionalId,
        string $title,
        ?string $caseNumber,
        ?string $area,
        string $status,
        string $priority,
        ?string $notes
    ): self {
        return new self(0, $companyId, $clientId, $professionalId, $title, $caseNumber, $area, $status, $priority, $notes);
    }

    public static function restore(
        int $id,
        int $companyId,
        ?int $clientId,
        ?int $professionalId,
        string $title,
        ?string $caseNumber,
        ?string $area,
        string $status,
        string $priority,
        ?string $notes
    ): self {
        return new self($id, $companyId, $clientId, $professionalId, $title, $caseNumber, $area, $status, $priority, $notes);
    }

    public function id(): int { return $this->id; }
    public function companyId(): int { return $this->companyId; }
    public function clientId(): ?int { return $this->clientId; }
    public function professionalId(): ?int { return $this->professionalId; }
    public function title(): string { return $this->title; }
    public function caseNumber(): ?string { return $this->caseNumber; }
    public function area(): ?string { return $this->area; }
    public function status(): string { return $this->status; }
    public function priority(): string { return $this->priority; }
    public function notes(): ?string { return $this->notes; }
}
