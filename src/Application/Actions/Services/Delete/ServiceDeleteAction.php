<?php 

namespace App\Application\Actions\Services\Delete;

use App\Application\Actions\Action;
use App\Domain\Services\Services\ServiceServices;

final class ServiceDeleteAction extends Action {

    public function __construct(private readonly ServiceServices $serviceServices){}

    public function action(): \Psr\Http\Message\ResponseInterface {
        $id = (int) $this->resolveArg('id');
        $this->serviceServices->delete($id);

        return $this->respondWithData(['message' => 'Service deleted successfully.']);
    }
    

}