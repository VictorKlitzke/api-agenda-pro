<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use App\Domain\User\Services\LoginUserService;
use App\Domain\User\Data\DTOs\Request\LoginUserRequest;
use Psr\Http\Message\ResponseInterface as Response;

final class LoginAction extends Action
{
    private LoginUserService $service;

    public function __construct(LoginUserService $service)
    {
        $this->service = $service;
    }

    protected function action(): Response
    {
        $data = (array) $this->getFormData();

        $loginRequest = LoginUserRequest::fromArray($data);

        $user = $this->service->execute($loginRequest);

        if (!$user) {
            return $this->respondWithData(['error' => 'Credenciais inválidas'], 401);
        }

        return $this->respondWithData([
            'accessToken' => session_id(),
            'user' => [
                'id' => $user->id(),
                'name' => $user->name(),
                'email' => $user->email(),
            ]
        ], 200);
    }
}
