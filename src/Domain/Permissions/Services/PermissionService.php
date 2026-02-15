<?php

declare(strict_types=1);

namespace App\Domain\Permissions\Services;

use App\Domain\Permissions\Repositories\PermissionRepository;

final class PermissionService
{
    public function __construct(private PermissionRepository $permissions) {}

    public function list(): array
    {
        return $this->permissions->findAll();
    }
}
