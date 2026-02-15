<?php 

namespace App\Application\Actions\Profissionals\List;

use App\Application\Actions\Action;
use App\Domain\Profissionals\Services\ProfissionalService;

final class ProfissionalListByIdAction extends Action {

    public function __construct(private readonly ProfissionalService $service){}

    public function action(): \Psr\Http\Message\ResponseInterface {
        $id = (int) $this->resolveArg('id');
        $profissional = $this->service->find($id);

        return $this->respondWithData($profissional);
    }
    
}