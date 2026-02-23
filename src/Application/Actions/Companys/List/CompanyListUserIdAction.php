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
    return $this->respondWithData($company, 200);
    }
}