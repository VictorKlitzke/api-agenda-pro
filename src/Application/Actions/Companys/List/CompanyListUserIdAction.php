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

    $userId = $this->request->getAttribute(name: 'userId');
    $result = $this->companyService->findByUserId(userId: $userId);

    return $this->respondWithData($result);

    }
}