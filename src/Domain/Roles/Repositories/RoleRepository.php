<?php

declare(strict_types=1);

namespace App\Domain\Roles\Repositories;

use Illuminate\Database\Connection;

final class RoleRepository
{
    public function __construct(private Connection $connection) {}

    public function findByName(int $companyId, string $name): ?array
    {
        $row = $this->connection->table('roles')
            ->where('company_id', $companyId)
            ->where('name', $name)
            ->first();

        return $row ? (array) $row : null;
    }

    public function create(int $companyId, string $name, ?string $description = null, bool $isDefault = false): int
    {
        return (int) $this->connection->table('roles')->insertGetId([
            'company_id' => $companyId,
            'name' => $name,
            'description' => $description,
            'is_default' => $isDefault ? 1 : 0,
        ]);
    }
}
