<?php 

namespace App\Application\Actions\Services\Update;

use App\Application\Actions\Action;
use App\Domain\Services\Services\ServiceServices;
use App\Domain\Services\Data\DTOs\Request\ServiceRequest;

final class ServiceUpdateAction extends Action
{
    public function __construct(private readonly ServiceServices $serviceServices){}
    protected function action(): \Psr\Http\Message\ResponseInterface {
        $id = (int) $this->resolveArg('id');
        $request = ServiceRequest::fromArray(data: $this->request->getParsedBody());
        $service = $this->serviceServices->update($request, id: $id);

        return $this->respondWithData($service);
    }
    
}