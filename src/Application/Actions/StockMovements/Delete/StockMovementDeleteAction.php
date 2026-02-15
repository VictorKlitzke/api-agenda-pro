<?php

declare(strict_types=1);

namespace App\Application\Actions\StockMovements\Delete;

use App\Application\Actions\Action;
use App\Domain\StockMovements\Services\StockMovementService;
use Psr\Http\Message\ResponseInterface;

final class StockMovementDeleteAction extends Action
{
    public function __construct(private StockMovementService $service)
    {
    }

    protected function action(): ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $deleted = $this->service->delete($id);

        return $this->respondWithData(['deleted' => $deleted]);
    }
}
