<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Domain\User\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpUnauthorizedException;

final class RoleMiddleware implements Middleware
{
    /** @var string[] */
    private array $allowedRoles;

    public function __construct(
        private readonly UserRepository $users,
        array $allowedRoles
    ) {
        $this->allowedRoles = array_map('strtoupper', $allowedRoles);
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $userId = $request->getAttribute('userId');
        if (!$userId) {
            throw new HttpUnauthorizedException($request, 'Usuário não autenticado');
        }

        $user = $this->users->findById((int) $userId);
        if (!$user) {
            throw new HttpUnauthorizedException($request, 'Usuário não encontrado');
        }

        $role = strtoupper($user->tipoConta());
        if (!in_array($role, $this->allowedRoles, true)) {
            throw new HttpForbiddenException($request, 'Acesso negado');
        }

        return $handler->handle($request);
    }
}
