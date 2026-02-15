<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Domain\Auth\Repositories\UserTokenRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;

final class AuthMiddleware implements Middleware
{
    public function __construct(private readonly UserTokenRepository $tokens)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $handler->handle($request);
        }

        $authHeader = $request->getHeaderLine('Authorization');
        if ($authHeader === '' || !str_starts_with($authHeader, 'Bearer ')) {
            throw new HttpUnauthorizedException($request, 'Token ausente');
        }

        $token = trim(substr($authHeader, 7));
        if ($token === '') {
            throw new HttpUnauthorizedException($request, 'Token inválido');
        }

        $userId = $this->tokens->findUserByToken($token);
        if (!$userId) {
            throw new HttpUnauthorizedException($request, 'Token expirado ou inválido');
        }

        return $handler->handle($request->withAttribute('userId', $userId));
    }
}
