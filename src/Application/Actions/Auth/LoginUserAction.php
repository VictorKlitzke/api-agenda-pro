<?php
namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use App\Domain\User\Data\DTOs\Request\LoginUserRequest;
use App\Domain\User\Services\LoginUserService;
use Psr\Http\Message\ResponseInterface;

final class LoginUserAction extends Action
{
    public function __construct(
        private LoginUserService $service
    ) {
    }

    public function action(): ResponseInterface
    {
        $data = (array) $this->request->getParsedBody();

        $loginRequest = LoginUserRequest::fromArray($data);

        $user = $this->service->execute($loginRequest);

        if (!$user) return $this->respondWithData(['error' => 'Credenciais inválidas'], 401);
        

        $token = null;
        $token = $_SESSION['access_token'] ?? session_id();

        return $this->respondWithData([
            'accessToken' => $token,
            'user' => [
                'id' => $user->id(),
                'name' => $user->name(),
                'email' => $user->email(),
                'tipoConta' => $user->tipoConta(),
            ]
        ], 200);
    }
}
