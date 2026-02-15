<?php 

namespace App\Application\Actions\Produtcs\Update;

use App\Application\Actions\Action;
use App\Domain\Products\Data\DTOs\Request\PreductRequest;
use App\Domain\Products\Services\ProductService;
use Psr\Http\Message\ResponseInterface;

final class ProductUpdateAction extends Action
{
    public function __construct(private ProductService $productService)
    {
    }

    protected function action(): ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $data = (array) $this->getFormData();
        $productRequest = PreductRequest::fromArray($data);
        $updatedProduct = $this->productService->update($productRequest, $id);

        if (!$updatedProduct) {
            return $this->respondWithData(['error' => 'Produto não encontrado'], 404);
        }

        return $this->respondWithData($updatedProduct);
    }
}