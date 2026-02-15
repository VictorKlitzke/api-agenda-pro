<?php

namespace App\Application\Actions\Produtcs\List;

use App\Application\Actions\Action;
use App\Domain\Products\Services\ProductService;
use Psr\Http\Message\ResponseInterface;

final class ProductListByIdAction extends Action
{
    public function __construct(private ProductService $productService)
    {
    }
    protected function action(): ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $product = $this->productService->findById($id);

        return $this->respondWithData($product);
    }
}