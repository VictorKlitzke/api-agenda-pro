<?php 

namespace App\Application\Actions\Services\List;

use App\Application\Actions\Action;
use App\Domain\Services\Services\ServiceServices;
use App\Domain\Company\Repositories\CompanyRepository;

final class ServiceListAction extends Action {

    public function __construct(
        private readonly ServiceServices $serviceServices,
        private readonly CompanyRepository $companies
    ){}

    public function action(): \Psr\Http\Message\ResponseInterface {
        $companyId = (int) ($this->resolveArg('companyId') ?? 0);
        $services = $this->serviceServices->findAllByCompanyId(companyId: $companyId);
        return $this->respondWithData($services);
    }
    
}