<?php 

namespace App\Application\Actions\Services\List;

use App\Application\Actions\Action;
use App\Domain\Services\Services\ServiceServices;

final class ServiceListByIdaction extends Action {

    public function __construct(private readonly ServiceServices $serviceServices){}

    public function action(): \Psr\Http\Message\ResponseInterface {
        $id = (int) $this->resolveArg('id');
        $service = $this->serviceServices->findById($id);

        return $this->respondWithData($service);
    }
    
}