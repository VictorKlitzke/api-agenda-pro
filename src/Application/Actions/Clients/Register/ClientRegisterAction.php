<?php 
namespace App\Application\Actions\Clients\Register; 

use App\Application\Actions\Action;
use App\Domain\Clients\Data\DTOs\Request\ClientRequest;
use App\Domain\Clients\Services\ClientService;
use Psr\Http\Message\ResponseInterface;


final class ClientRegisterAction extends Action {

        public function __construct(private ClientService $clientService) {

        }

        protected function action(): ResponseInterface
        {
            $data = $this->request->getParsedBody();

            $request = ClientRequest::fromArray(data: $data);
    
            $client = $this->clientService->register(client: $request);
    
            return $this->respondWithData($client);
        }

}