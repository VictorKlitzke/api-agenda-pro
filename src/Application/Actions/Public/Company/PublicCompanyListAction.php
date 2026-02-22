<?php

declare(strict_types=1);

namespace App\Application\Actions\Public\Company;

use App\Application\Actions\Action;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\Settings\Repositories\SettingsRepository;
use Psr\Http\Message\ResponseInterface;

final class PublicCompanyListAction extends Action
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly SettingsRepository $settings
    ) {}

    public function action(): ResponseInterface
    {
        $rows = $this->companies->findAll();

        $items = array_map(function ($company) {
            $settings = $this->settings->findByCompanyId($company->id()) ?? [];
            $brandName = $settings['brand_name'] ?? null;

            return [
                'id' => $company->id(),
                'name' => $brandName ?: $company->name(),
            ];
        }, $rows);

        return $this->respondWithData($items);
    }
}
