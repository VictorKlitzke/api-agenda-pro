<?php
declare(strict_types=1);

namespace App\Domain\User\Services;

use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Data\DTOs\Request\LoginUserRequest;
use App\Domain\User\Entities\UserEntity;
use App\Domain\Auth\Repositories\LoginAttemptRepository;
use App\Domain\Auth\Repositories\UserTokenRepository;

final class LoginUserService
{
    public function __construct(
        private UserRepository $users,
        private LoginAttemptRepository $attempts,
        private UserTokenRepository $tokens
    ) {}

    public function execute(LoginUserRequest $request): ?UserEntity
    {
        $email = $request->email();

        if ($this->attempts->isLocked($email)) {
            return null;
        }

        $user = $this->users->findByEmail(email: $email);

        if (!$user) {
            $this->attempts->recordFailure($email);
            return null;
        }

        if (!password_verify(password: $request->password(), hash: $user->passwordHash())) {
            $this->attempts->recordFailure($email);
            return null;
        }

        $this->attempts->resetAttempts($email);

        $token = bin2hex(random_bytes(32));
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $this->tokens->createToken($user->id(), $token, $expiresAt);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id();
        $_SESSION['access_token'] = $token;

        return $user;
    }
}