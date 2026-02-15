<?php

declare(strict_types=1);

namespace App\Domain\Roles\Repositories;

use Illuminate\Database\Connection;

final class ProfessionalRoleRepository
{
    public function __construct(private Connection $connection) {}

    public function findRoleId(int $professionalId): ?int
    {
        $row = $this->connection->table('professional_roles')
            ->where('professional_id', $professionalId)
            ->first();

        return $row ? (int) $row->role_id : null;
    }

    public function getCompanyId(int $professionalId): ?int
    {
        $row = $this->connection->table('profissionals')
            ->where('id', $professionalId)
            ->first();

        return $row ? (int) $row->company_id : null;
    }

    public function setRole(int $professionalId, int $roleId, int $companyId): void
    {
        $this->connection->table('professional_roles')->where('professional_id', $professionalId)->delete();
        $this->connection->table('professional_roles')->insert([
            'professional_id' => $professionalId,
            'role_id' => $roleId,
            'company_id' => $companyId,
        ]);
    }
}
