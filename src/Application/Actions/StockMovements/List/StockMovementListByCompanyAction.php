<?php

declare(strict_types=1);

namespace App\Application\Actions\StockMovements\List;

use App\Application\Actions\Action;
use App\Domain\StockMovements\Services\StockMovementService;
use Psr\Http\Message\ResponseInterface;

final class StockMovementListByCompanyAction extends Action
{
    public function __construct(private StockMovementService $service)
    {
    }

    protected function action(): ResponseInterface
    {
        $companyId = (int) $this->resolveArg('companyId');
        $data = $this->service->findByCompanyId($companyId);

        return $this->respondWithData($data);
    }
}
