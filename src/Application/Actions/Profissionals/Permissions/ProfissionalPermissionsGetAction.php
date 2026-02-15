<?php

declare(strict_types=1);

namespace App\Application\Actions\Profissionals\Permissions;

use App\Application\Actions\Action;
use App\Domain\Roles\Services\ProfessionalPermissionService;

final class ProfissionalPermissionsGetAction extends Action
{
    public function __construct(private readonly ProfessionalPermissionService $service) {}

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $keys = $this->service->getPermissions($id);

        return $this->respondWithData(['permissions' => array_values($keys)]);
    }
}
