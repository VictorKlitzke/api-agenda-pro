<?php 

namespace App\Application\Actions\Clients\List;

use App\Application\Actions\Action;
use App\Domain\Clients\Services\ClientService;
use App\Domain\Company\Repositories\CompanyRepository;

final class ClientListAction extends Action {

    public function __construct(
        private readonly ClientService $clientService,
        private readonly CompanyRepository $companies
    ) {
    }
    public function action(): \Psr\Http\Message\ResponseInterface {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData([]);
        }

        $clients = $this->clientService->findAllByCompanyId($companyId);
        return $this->respondWithData($clients);
    }
}