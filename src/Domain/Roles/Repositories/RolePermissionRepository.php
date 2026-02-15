<?php

declare(strict_types=1);

namespace App\Domain\Roles\Repositories;

use Illuminate\Database\Connection;

final class RolePermissionRepository
{
    public function __construct(private Connection $connection) {}

    public function setPermissions(int $roleId, array $permissionIds): void
    {
        $this->connection->table('role_permissions')->where('role_id', $roleId)->delete();

        if (empty($permissionIds)) {
            return;
        }

        $rows = array_map(fn($id) => ['role_id' => $roleId, 'permission_id' => (int) $id], $permissionIds);
        $this->connection->table('role_permissions')->insert($rows);
    }

    public function getPermissionKeys(int $roleId): array
    {
        return $this->connection->table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role_id', $roleId)
            ->pluck('permissions.key')
            ->toArray();
    }
}
