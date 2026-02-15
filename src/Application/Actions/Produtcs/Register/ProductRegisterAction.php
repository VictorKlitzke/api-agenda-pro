<?php
namespace App\Application\Actions\Produtcs\Register;

use App\Application\Actions\Action;
use App\Domain\Products\Data\DTOs\Request\PreductRequest;
use App\Domain\Products\Services\ProductService;

class ProductRegisterAction extends Action
{
    public function __construct(private readonly ProductService $services)
    {
    }

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $data = (array) $this->request->getParsedBody();
        $request = PreductRequest::fromArray(data: $data);
        $product = $this->services->register(preductRequest: $request);
        return $this->respondWithData(data: $product, statusCode: 201);
    }
}