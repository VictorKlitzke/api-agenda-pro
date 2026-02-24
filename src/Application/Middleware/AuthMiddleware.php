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

        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }

        $token = $this->resolveTokenFromSessionOrHeader($request);
        if ($token === null) {
            throw new HttpUnauthorizedException($request, 'Sessão não autenticada');
        }

        $userId = $this->tokens->findUserByToken($token);
        if (!$userId) {
            throw new HttpUnauthorizedException($request, 'Token expirado ou inválido');
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['access_token'] = $token;
        }

        return $handler->handle($request->withAttribute('userId', $userId));
    }

    private function resolveTokenFromSessionOrHeader(Request $request): ?string
    {
        $sessionToken = $_SESSION['access_token'] ?? null;
        if (is_string($sessionToken) && trim($sessionToken) !== '') {
            return trim($sessionToken);
        }

        $allowBearerFallback = filter_var($_ENV['AUTH_ALLOW_BEARER_FALLBACK'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
        if (!$allowBearerFallback) {
            return null;
        }

        $authHeader = $request->getHeaderLine('Authorization');
        if ($authHeader === '' || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = trim(substr($authHeader, 7));
        return $token !== '' ? $token : null;
    }
}
