<?php

declare(strict_types=1);

namespace App\Application\Actions\Profissionals\Permissions;

use App\Application\Actions\Action;
use App\Domain\Roles\Services\ProfessionalPermissionService;

final class ProfissionalPermissionsUpdateAction extends Action
{
    public function __construct(private readonly ProfessionalPermissionService $service) {}

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $data = (array) $this->request->getParsedBody();
        $permissions = $data['permissions'] ?? [];
        if (!is_array($permissions)) {
            $permissions = [];
        }

        $this->service->setPermissions($id, $permissions);

        return $this->respondWithData(['success' => true]);
    }
}
