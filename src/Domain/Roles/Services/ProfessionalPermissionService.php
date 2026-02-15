<?php

declare(strict_types=1);

namespace App\Domain\Roles\Services;

use App\Domain\Permissions\Repositories\PermissionRepository;
use App\Domain\Roles\Repositories\RolePermissionRepository;
use App\Domain\Roles\Repositories\RoleRepository;
use App\Domain\Profissionals\Repositories\ProfissionalRepository;

final class ProfessionalPermissionService
{
    public function __construct(
        private RoleRepository $roles,
        private PermissionRepository $permissions,
        private RolePermissionRepository $rolePermissions,
        private ProfissionalRepository $professionals
    ) {}

    public function getPermissions(int $professionalId): array
    {
        $companyId = $this->professionals->getCompanyId($professionalId);
        if (!$companyId) {
            return [];
        }

        $roleName = 'PROF_' . $professionalId;
        $role = $this->roles->findByName($companyId, $roleName);
        if (!$role) {
            return [];
        }

        return $this->rolePermissions->getPermissionKeys((int) $role['id']);
    }

    public function setPermissions(int $professionalId, array $permissionKeys): void
    {
        $companyId = $this->professionals->getCompanyId($professionalId);
        if (!$companyId) {
            throw new \RuntimeException('Profissional não encontrado');
        }

        $roleName = 'PROF_' . $professionalId;
        $role = $this->roles->findByName($companyId, $roleName);
        $roleId = $role ? (int) $role['id'] : $this->roles->create($companyId, $roleName, 'Permissões do profissional');

        $permissionRows = $this->permissions->findByKeys($permissionKeys);
        $permissionIds = array_map(fn($row) => (int) $row['id'], $permissionRows);

        $this->rolePermissions->setPermissions($roleId, $permissionIds);
    }
}
