<?php
declare(strict_types=1);

namespace App\Domain\User\Services;

use App\Domain\User\Data\DTOs\Request\VerifyEmailCodeRequest;
use App\Domain\User\Repositories\UserRepository;
use App\Infrastructure\Exceptions\ValidationException;

final class VerifyEmailCodeService
{
    public function __construct(private UserRepository $users)
    {
    }

    public function execute(VerifyEmailCodeRequest $request): void
    {
        $verified = $this->users->verifyEmailCode(
            email: $request->email(),
            verificationCode: $request->verificationCode()
        );

        if (!$verified) {
            throw new ValidationException([
                'verification_code' => 'Código inválido para este email.',
            ], 'Código de verificação inválido.');
        }
    }
}
