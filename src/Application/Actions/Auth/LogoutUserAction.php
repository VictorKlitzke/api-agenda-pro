<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use App\Domain\Auth\Repositories\UserTokenRepository;
use Psr\Http\Message\ResponseInterface;

final class LogoutUserAction extends Action
{
    public function __construct(private UserTokenRepository $tokens)
    {
    }

    protected function action(): ResponseInterface
    {
        $token = $_SESSION['access_token'] ?? null;
        if (is_string($token) && trim($token) !== '') {
            $this->tokens->revokeToken(trim($token));
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 42000,
                    'path' => $params['path'] ?? '/',
                    'domain' => $params['domain'] ?? '',
                    'secure' => (bool) ($params['secure'] ?? false),
                    'httponly' => (bool) ($params['httponly'] ?? true),
                    'samesite' => $params['samesite'] ?? 'Lax',
                ]
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        return $this->respondWithData(['message' => 'Logout realizado com sucesso']);
    }
}
