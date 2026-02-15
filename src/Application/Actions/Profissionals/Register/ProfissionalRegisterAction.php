<?php 
namespace App\Application\Actions\Profissionals\Register;

use App\Application\Actions\Action;
use App\Domain\Profissionals\Data\DTOs\Request\ProfissionalRequest;
use App\Domain\Profissionals\Services\ProfissionalService;

final class ProfissionalRegisterAction extends Action
{
    public function __construct(private readonly ProfissionalService $services){}

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $data = (array) $this->request->getParsedBody();
        $request = ProfissionalRequest::fromArray(data: $data);
        $profissional = $this->services->register(request: $request);
        return $this->respondWithData([
            'success' => $profissional
        ]);
    }
}