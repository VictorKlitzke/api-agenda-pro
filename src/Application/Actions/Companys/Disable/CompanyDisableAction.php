<?php 

namespace App\Application\Actions\Companys\Disable;

use App\Application\Actions\Action;
use App\Domain\Company\Services\CompanyService;

final class CompanyDisableAction extends Action{
    public function __construct(private CompanyService $companyService)
    {
        //code...
    }

    public function action(): \Psr\Http\Message\ResponseInterface {
        $id = (int) $this->resolveArg('id');
        $companys = $this->companyService->deactivate(id: $id);
        return $this->respondWithData($companys);
    }
}