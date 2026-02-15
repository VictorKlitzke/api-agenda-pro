<?php 


namespace App\Application\Actions\Produtcs\List;

use App\Application\Actions\Action;
use App\Domain\Products\Services\ProductService;
use App\Domain\Company\Repositories\CompanyRepository;
use Psr\Http\Message\ResponseInterface;

final class ProductListAction extends Action
{
    public function __construct(
        private ProductService $productService,
        private CompanyRepository $companies
    )
    {
    }
    protected function action(): ResponseInterface
    {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData([]);
        }

        $products = $this->productService->findAllByCompanyId($companyId);

        return $this->respondWithData(data: $products);
    }
}