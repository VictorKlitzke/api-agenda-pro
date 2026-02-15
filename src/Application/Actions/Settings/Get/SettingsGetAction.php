<?php

declare(strict_types=1);

namespace App\Application\Actions\Settings\Get;

use App\Application\Actions\Action;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\Settings\Services\SettingsService;

final class SettingsGetAction extends Action
{
    public function __construct(
        private readonly SettingsService $service,
        private readonly CompanyRepository $companies
    ) {}

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData(null);
        }

        $settings = $this->service->getByCompanyId($companyId);

        return $this->respondWithData($settings);
    }
}
