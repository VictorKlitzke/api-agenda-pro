<?php
declare(strict_types=1);

namespace App\Domain\User\Services;

use App\Domain\User\Interfaces\UserInterface;
use App\Domain\User\Data\DTOs\Request\LoginUserRequest;
use App\Domain\User\Entities\UserEntity;

final class LoginUserService
{
    public function __construct(
        private UserInterface $users
    ) {}

    public function execute(LoginUserRequest $request): ?UserEntity
    {
        $user = $this->users->findByEmail(email: $request->email());

        if (!$user) {
            return null;
        }

        if (!password_verify(password: $request->password(), hash: $user->passwordHash())) {
            return null;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user->id();

        return $user;
    }
}