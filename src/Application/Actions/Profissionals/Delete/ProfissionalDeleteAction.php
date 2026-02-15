<?php 

namespace App\Application\Actions\Profissionals\Delete;

use App\Application\Actions\Action;
use App\Domain\Profissionals\Services\ProfissionalService;

final class ProfissionalDeleteAction extends Action {

    public function __construct(private readonly ProfissionalService $service){}

    public function action(): \Psr\Http\Message\ResponseInterface {
        $id = (int) $this->resolveArg('id');
        $deleted = $this->service->delete($id);

        return $this->respondWithData(['success' => $deleted]);
    }
    
}
