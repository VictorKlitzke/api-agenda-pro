<?php 

namespace App\Domain\Agendamentos\Data\DTOs\Request;

final class AgendamentoRequest
{
    public function __construct(
        private int $companyId,
        private int $professionalId,
        private int $clientId,
        private int $serviceId,
        private string $startAt,
        private ?string $endAt,
        private int $durationMinutes,
        private ?string $notes,
        private ?bool $active,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            companyId: (int) ($data['companyId'] ?? $data['company_id'] ?? 0),
            professionalId: (int) ($data['professionalId'] ?? $data['professional_id'] ?? 0),
            clientId: (int) ($data['clientId'] ?? $data['client_id'] ?? 0),
            serviceId: (int) ($data['serviceId'] ?? $data['service_id'] ?? 0),
            startAt: (string) ($data['startAt'] ?? $data['start_at'] ?? ''),
            endAt: isset($data['endAt']) ? (string) $data['endAt'] : (isset($data['end_at']) ? (string) $data['end_at'] : null),
            durationMinutes: (int) ($data['durationMinutes'] ?? $data['duration_minutes'] ?? 0),
            notes: $data['notes'] ?? $data['observation'] ?? null,
            active: isset($data['active']) ? (bool) $data['active'] : null,
        );
    }

    public function companyId(): int { return $this->companyId; }
    public function professionalId(): int { return $this->professionalId; }
    public function clientId(): int { return $this->clientId; }
    public function serviceId(): int { return $this->serviceId; }
    public function startAt(): string { return $this->startAt; }
    public function endAt(): ?string { return $this->endAt; }
    public function durationMinutes(): int { return $this->durationMinutes; }
    public function notes(): ?string { return $this->notes; }
    public function active(): ?bool { return $this->active; }
}
