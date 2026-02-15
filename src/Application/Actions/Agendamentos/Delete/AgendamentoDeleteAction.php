<?php 

namespace App\Application\Actions\Agendamentos\Delete;

use App\Application\Actions\Action;
use App\Domain\Agendamentos\Services\AgendamentoService;

final class AgendamentoDeleteAction extends Action
{
    public function __construct(private readonly AgendamentoService $service){}

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $deleted = $this->service->delete($id);

        return $this->respondWithData(['success' => $deleted]);
    }
}
