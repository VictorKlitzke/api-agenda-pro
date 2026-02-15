<?php 

namespace App\Application\Actions\Profissionals\Update;

use App\Application\Actions\Action;
use App\Domain\Profissionals\Data\DTOs\Request\ProfissionalRequest;
use App\Domain\Profissionals\Services\ProfissionalService;

final class ProfissionalUpdateAction extends Action
{
    public function __construct(private readonly ProfissionalService $service){}

    protected function action(): \Psr\Http\Message\ResponseInterface {
        $id = (int) $this->resolveArg('id');
        $request = ProfissionalRequest::fromArray(data: (array) $this->request->getParsedBody());
        $updated = $this->service->update($id, $request);

        return $this->respondWithData(['success' => $updated]);
    }
    
}
