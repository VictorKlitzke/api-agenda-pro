<?php

declare(strict_types=1);

namespace App\Application\Actions\StockMovements\Register;

use App\Application\Actions\Action;
use App\Domain\StockMovements\Data\DTOs\Request\StockMovementRequest;
use App\Domain\StockMovements\Services\StockMovementService;

final class StockMovementRegisterAction extends Action
{
    public function __construct(private readonly StockMovementService $service)
    {
    }

    protected function action(): \Psr\Http\Message\ResponseInterface
    {
        $data = (array) $this->getFormData();
        $request = StockMovementRequest::fromArray($data);
        $movement = $this->service->create($request);

        return $this->respondWithData($movement, 201);
    }
}
