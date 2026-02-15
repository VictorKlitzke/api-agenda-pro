<?php 

namespace App\Application\Actions\Profissionals\List;

use App\Application\Actions\Action;
use App\Domain\Profissionals\Services\ProfissionalService;
use App\Domain\Company\Repositories\CompanyRepository;

final class ProfissionalListAction extends Action {

    public function __construct(
        private readonly ProfissionalService $service,
        private readonly CompanyRepository $companies
    ){}

    public function action(): \Psr\Http\Message\ResponseInterface {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData([]);
        }

        $profissionals = $this->service->findAllByCompanyId($companyId);

        return $this->respondWithData($profissionals);
    }
    
}