<?php

declare(strict_types=1);

namespace App\Domain\Cases\Data\DTOs\Request;

final class CaseRequest
{
    public function __construct(
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

    public static function fromArray(array $data): self
    {
        return new self(
            companyId: (int) ($data['companyId'] ?? $data['company_id'] ?? 0),
            clientId: isset($data['clientId']) ? (int) $data['clientId'] : (isset($data['client_id']) ? (int) $data['client_id'] : null),
            professionalId: isset($data['professionalId']) ? (int) $data['professionalId'] : (isset($data['professional_id']) ? (int) $data['professional_id'] : null),
            title: (string) ($data['title'] ?? ''),
            caseNumber: $data['caseNumber'] ?? $data['case_number'] ?? null,
            area: $data['area'] ?? null,
            status: (string) ($data['status'] ?? 'Ativo'),
            priority: (string) ($data['priority'] ?? 'Normal'),
            notes: $data['notes'] ?? null
        );
    }

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
