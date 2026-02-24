<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class SessionMiddleware implements Middleware
{
    private bool $configured = false;

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!$this->configured) {
            $this->configureSessionCookie();
            $this->configured = true;
        }

        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }

        $request = $request->withAttribute('session', $_SESSION ?? []);

        return $handler->handle($request);
    }

    private function configureSessionCookie(): void
    {
        $secure = filter_var($_ENV['SESSION_COOKIE_SECURE'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
        $httpOnly = filter_var($_ENV['SESSION_COOKIE_HTTP_ONLY'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $sameSite = (string) ($_ENV['SESSION_COOKIE_SAME_SITE'] ?? 'Lax');
        $path = (string) ($_ENV['SESSION_COOKIE_PATH'] ?? '/');
        $domain = trim((string) ($_ENV['SESSION_COOKIE_DOMAIN'] ?? ''));
        $lifetime = (int) ($_ENV['SESSION_COOKIE_LIFETIME'] ?? 0);
        $name = trim((string) ($_ENV['SESSION_COOKIE_NAME'] ?? 'agenda_session'));

        session_name($name !== '' ? $name : 'agenda_session');
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
        ]);
    }
}
