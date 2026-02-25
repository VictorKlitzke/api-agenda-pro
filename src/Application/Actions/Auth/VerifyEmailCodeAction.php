<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use App\Domain\User\Data\DTOs\Request\VerifyEmailCodeRequest;
use App\Domain\User\Services\VerifyEmailCodeService;
use Psr\Http\Message\ResponseInterface as Response;

final class VerifyEmailCodeAction extends Action
{
    public function __construct(private VerifyEmailCodeService $service)
    {
    }

    protected function action(): Response
    {
        $data = (array) $this->getFormData();
        $verifyRequest = VerifyEmailCodeRequest::fromArray($data);
        $this->service->execute($verifyRequest);

        return $this->respondWithData([
            'message' => 'Email verificado com sucesso',
        ], 200);
    }
}
