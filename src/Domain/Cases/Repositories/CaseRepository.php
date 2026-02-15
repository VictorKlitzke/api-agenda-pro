<?php

declare(strict_types=1);

namespace App\Domain\Cases\Repositories;

use App\Domain\Cases\Entities\CaseEntity;
use Illuminate\Database\Connection;

final class CaseRepository
{
    public function __construct(private readonly Connection $connection) {}

    public function save(CaseEntity $case): array
    {
        $id = $this->connection->table('cases')->insertGetId([
            'company_id' => $case->companyId(),
            'client_id' => $case->clientId(),
            'professional_id' => $case->professionalId(),
            'title' => $case->title(),
            'case_number' => $case->caseNumber(),
            'area' => $case->area(),
            'status' => $case->status(),
            'priority' => $case->priority(),
            'notes' => $case->notes(),
        ]);

        return $this->findById((int) $id) ?? [];
    }

    public function update(CaseEntity $case, int $id): bool
    {
        return $this->connection->table('cases')
            ->where('id', $id)
            ->update([
                'company_id' => $case->companyId(),
                'client_id' => $case->clientId(),
                'professional_id' => $case->professionalId(),
                'title' => $case->title(),
                'case_number' => $case->caseNumber(),
                'area' => $case->area(),
                'status' => $case->status(),
                'priority' => $case->priority(),
                'notes' => $case->notes(),
            ]) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->connection->table('cases')
            ->where('id', $id)
            ->delete() > 0;
    }

    public function findAllByCompanyId(int $companyId): array
    {
        return $this->connection->table('cases')
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();
    }

    public function findById(int $id): ?array
    {
        $row = $this->connection->table('cases')
            ->where('id', $id)
            ->first();

        return $row ? (array) $row : null;
    }
}
