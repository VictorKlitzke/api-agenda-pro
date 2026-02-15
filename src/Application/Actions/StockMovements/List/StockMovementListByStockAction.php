<?php

declare(strict_types=1);

namespace App\Application\Actions\StockMovements\List;

use App\Application\Actions\Action;
use App\Domain\StockMovements\Services\StockMovementService;
use Psr\Http\Message\ResponseInterface;

final class StockMovementListByStockAction extends Action
{
    public function __construct(private StockMovementService $service)
    {
    }

    protected function action(): ResponseInterface
    {
        $stockId = (int) $this->resolveArg('stockId');
        $data = $this->service->findByStockId($stockId);

        return $this->respondWithData($data);
    }
}
