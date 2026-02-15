<?php 
namespace App\Application\Actions\Companys\List;

use App\Application\Actions\Action;
use App\Domain\Company\Services\CompanyService;
use App\Domain\Company\Repositories\CompanyRepository;

final class CompanyListAction extends Action{
    public function __construct(
        private CompanyService $companyService,
        private CompanyRepository $companies
    )
    {
        //code...
    }

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        if ($userId === 0) {
            return $this->respondWithData([]);
        }

        $company = $this->companies->findEntityByUserId($userId);
        if (!$company) {
            return $this->respondWithData([]);
        }

        return $this->respondWithData([
            [
                'id' => $company->id(),
                'userId' => $company->userId(),
                'name' => $company->name(),
                'cnpj' => $company->cnpj(),
                'address' => $company->address(),
                'city' => $company->city(),
                'state' => $company->state(),
                'active' => $company->isActive(),
                'createdAt' => $company->createdAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $company->updatedAt()?->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}