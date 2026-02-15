<?php 

namespace App\Application\Actions\Companys\Update;
use App\Domain\Company\Data\DTOs\Request\UpdateCompanyRequest;


use App\Application\Actions\Action;
use App\Domain\Company\Services\CompanyService;

final class CompanyUpdateAction extends Action{
    public function __construct(private CompanyService $companyService)
    {
        //code...
    }

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $data = (array) $this->getFormData();
        $updateRequest = UpdateCompanyRequest::fromArray(data: $data); 
        $companys = $this->companyService->update(request: $updateRequest);
        return $this->respondWithData($companys);
    }
}