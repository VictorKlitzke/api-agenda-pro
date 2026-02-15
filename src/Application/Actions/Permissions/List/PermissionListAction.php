<?php

declare(strict_types=1);

namespace App\Application\Actions\Permissions\List;

use App\Application\Actions\Action;
use App\Domain\Permissions\Services\PermissionService;

final class PermissionListAction extends Action
{
    public function __construct(private readonly PermissionService $service) {}

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $rows = $this->service->list();

        return $this->respondWithData($rows);
    }
}
