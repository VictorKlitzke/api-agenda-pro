<?php 

namespace App\Application\Actions\Agendamentos\Update;

use App\Application\Actions\Action;
use App\Domain\Agendamentos\Data\DTOs\Request\AgendamentoRequest;
use App\Domain\Agendamentos\Services\AgendamentoService;

final class AgendamentoUpdateAction extends Action
{
    public function __construct(private readonly AgendamentoService $service){}

    protected function action(): \Psr\Http\Message\ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $request = AgendamentoRequest::fromArray((array) $this->request->getParsedBody());
        $updated = $this->service->update($id, $request);

        return $this->respondWithData(['success' => $updated]);
    }
}
