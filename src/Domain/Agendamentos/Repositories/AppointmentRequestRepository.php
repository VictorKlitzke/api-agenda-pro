<?php

declare(strict_types=1);

namespace App\Domain\Agendamentos\Repositories;

use Illuminate\Database\Connection;

final class AppointmentRequestRepository
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function create(array $data): array
    {
        $id = $this->connection->table('appointment_requests')->insertGetId([
            'company_id' => (int) ($data['company_id'] ?? 0),
            'client_id' => isset($data['client_id']) ? (int) $data['client_id'] : null,
            'client_name' => (string) ($data['client_name'] ?? ''),
            'client_email' => $data['client_email'] ?? null,
            'client_phone' => $data['client_phone'] ?? null,
            'service_id' => isset($data['service_id']) ? (int) $data['service_id'] : null,
            'preferred_date' => (string) ($data['preferred_date'] ?? ''),
            'preferred_time' => (string) ($data['preferred_time'] ?? ''),
            'notes' => $data['notes'] ?? null,
            'status' => (string) ($data['status'] ?? 'PENDING'),
            'appointment_id' => isset($data['appointment_id']) ? (int) $data['appointment_id'] : null,
        ]);

        return $this->findById((int) $id) ?? [];
    }

    public function findByCompanyId(int $companyId, ?string $status = null): array
    {
        $query = $this->connection->table('appointment_requests')
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get()->map(fn($row) => (array) $row)->toArray();
    }

    public function findById(int $id): ?array
    {
        $row = $this->connection->table('appointment_requests')
            ->where('id', $id)
            ->first();

        return $row ? (array) $row : null;
    }

    public function updateStatus(int $id, string $status, ?int $appointmentId = null): bool
    {
        return $this->connection->table('appointment_requests')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'appointment_id' => $appointmentId,
            ]) > 0;
    }
}
