<?php

declare(strict_types=1);

namespace App\Application\Actions\Cases\Register;

use App\Application\Actions\Action;
use App\Domain\Cases\Data\DTOs\Request\CaseRequest;
use App\Domain\Cases\Services\CaseService;
use App\Domain\Company\Repositories\CompanyRepository;
use Psr\Http\Message\ResponseInterface;

final class CaseRegisterAction extends Action
{
    public function __construct(
        private readonly CaseService $service,
        private readonly CompanyRepository $companies
    ) {}

    public function action(): ResponseInterface
    {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData(null, 400);
        }

        $data = (array) $this->request->getParsedBody();
        $data['companyId'] = $companyId;
        $request = CaseRequest::fromArray($data);
        $created = $this->service->register($request);

        return $this->respondWithData($created, 201);
    }
}
