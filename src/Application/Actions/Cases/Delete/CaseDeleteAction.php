<?php

declare(strict_types=1);

namespace App\Application\Actions\Cases\Delete;

use App\Application\Actions\Action;
use App\Domain\Cases\Services\CaseService;
use Psr\Http\Message\ResponseInterface;

final class CaseDeleteAction extends Action
{
    public function __construct(private readonly CaseService $service) {}

    public function action(): ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $deleted = $this->service->delete($id);

        return $this->respondWithData(['deleted' => $deleted]);
    }
}
