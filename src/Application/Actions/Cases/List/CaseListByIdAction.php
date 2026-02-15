<?php

declare(strict_types=1);

namespace App\Application\Actions\Cases\List;

use App\Application\Actions\Action;
use App\Domain\Cases\Services\CaseService;
use Psr\Http\Message\ResponseInterface;

final class CaseListByIdAction extends Action
{
    public function __construct(private readonly CaseService $service) {}

    public function action(): ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $case = $this->service->findById($id);

        return $this->respondWithData($case);
    }
}
