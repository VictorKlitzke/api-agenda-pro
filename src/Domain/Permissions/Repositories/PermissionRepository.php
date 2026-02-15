<?php

declare(strict_types=1);

namespace App\Domain\Permissions\Repositories;

use App\Domain\Permissions\Interfaces\PermissionInterface;
use Illuminate\Database\Connection;

final class PermissionRepository implements PermissionInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function findAll(): array
    {
        return $this->connection->table('permissions')->orderBy('key')->get()->toArray();
    }

    public function findByKeys(array $keys): array
    {
        return $this->connection->table('permissions')
            ->whereIn('key', $keys)
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();
    }
}
