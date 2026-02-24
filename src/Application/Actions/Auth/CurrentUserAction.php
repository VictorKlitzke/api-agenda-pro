<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use App\Domain\User\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface;

final class CurrentUserAction extends Action
{
    public function __construct(private UserRepository $users)
    {
    }

    protected function action(): ResponseInterface
    {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        if ($userId <= 0) {
            return $this->respondWithData(['error' => 'Não autenticado'], 401);
        }

        $user = $this->users->findById($userId);
        if ($user === null) {
            return $this->respondWithData(['error' => 'Usuário não encontrado'], 401);
        }

        return $this->respondWithData([
            'user' => [
                'id' => $user->id(),
                'name' => $user->name(),
                'email' => $user->email(),
                'tipoConta' => $user->tipoConta(),
            ],
        ]);
    }
}
