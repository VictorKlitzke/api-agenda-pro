<?php 
namespace App\Application\Actions\Produtcs\Delete;

use App\Application\Actions\Action;
use App\Domain\Products\Services\ProductService;
use Psr\Http\Message\ResponseInterface;

final class ProductDeleteAction extends Action
{
    public function __construct(private ProductService $productService)
    {
    }

    protected function action(): ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $this->productService->delete($id);

        return $this->respondWithData(['message' => 'Product deleted successfully.']);
    }
}   