<?php 

namespace App\Application\Actions\Clients\Update;

use App\Application\Actions\Action;
use App\Domain\Clients\Data\DTOs\Request\ClientRequest;
use App\Domain\Clients\Services\ClientService;


final class ClientUpdateAction extends Action {

    public function __construct(private ClientService $clientService){}

    protected function action(): \Psr\Http\Message\ResponseInterface
    {
        $data = $this->request->getParsedBody();

        $request = ClientRequest::fromArray(data: $data);

        $client = $this->clientService->update(client: $request, id: (int) $this->resolveArg('id'));

        return $this->respondWithData($client, 200 );
    }
}