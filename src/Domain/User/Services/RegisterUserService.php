<?php
declare(strict_types=1);

namespace App\Domain\User\Services;

use App\Domain\User\Entities\UserEntity;
use App\Domain\User\Events\UserRegisteredEvent;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Data\DTOs\Request\RegisterUserRequest;
use App\Infrastructure\Events\EventDispatcher;
use App\Infrastructure\Exceptions\ValidationException;

final class RegisterUserService
{
    public function __construct(
        private UserRepository $users,
        private EventDispatcher $dispatcher
    ) {
    }

    public function execute(RegisterUserRequest $request): void
    {
        $existing = $this->users->findByEmail($request->email());
        if ($existing !== null) {
            throw new ValidationException([
                'email' => 'Email já cadastrado.',
            ]);
        }

        $verificationCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = UserEntity::create(
            name: $request->name(),
            email: $request->email(),
            plainPassword: $request->password(),
            tipoConta: $request->tipoConta(),
            telefone: $request->telefone(),
            zipCode: $verificationCode
        );

        $user->deactivate();

        $this->users->save(user: $user, verificationCode: $verificationCode);

        try {
            $this->dispatcher->dispatch(new UserRegisteredEvent(
                userId: $user->id(),
                name: $user->name(),
                email: $user->email(),
                verificationCode: $verificationCode
            ));
        } catch (\Throwable) {
        }

    }
}