<?php 
namespace App\Application\Actions\Companys\List;

use App\Application\Actions\Action;
use App\Domain\Company\Services\CompanyService;
use Psr\Http\Message\ResponseInterface;


class CompanyListUserIdAction extends Action
{

    public function __construct(private CompanyService $companyService)
    {}

    public function action(): ResponseInterface{

    $userId = (int) $this->resolveArg('userId');
    $company = $this->companyService->findEntityByUserId(userId: $userId);
    if (!$company) {
        return $this->respondWithData(null, 200);
    }

    return $this->respondWithData($data = [
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
    ], statusCode: 200);

    }
}