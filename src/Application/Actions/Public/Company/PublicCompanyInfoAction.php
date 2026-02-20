<?php

declare(strict_types=1);

namespace App\Application\Actions\Public\Company;

use App\Application\Actions\Action;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\Settings\Repositories\SettingsRepository;
use App\Domain\Services\Services\ServiceServices;
use App\Domain\Profissionals\Repositories\ProfissionalRepository;
use Psr\Http\Message\ResponseInterface;

final class PublicCompanyInfoAction extends Action
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly SettingsRepository $settings,
        private readonly ServiceServices $services,
        private readonly ProfissionalRepository $professionals
    ) {}

    public function action(): ResponseInterface
    {
        $id = (int) $this->resolveArg('id');
        $company = $this->companies->findById($id);
        if (!$company) {
            return $this->respondWithData(null, 404);
        }

        $settings = $this->settings->findByCompanyId($id) ?? [];

        $brandName = $settings['brand_name'] ?? null;

        $services = $this->services->findAllByCompanyId($id);
        $professionals = $this->professionals->findAllByCompanyId($id);

        return $this->respondWithData([
            'id' => $company->id(),
            'name' => $brandName ?: $company->name(),
            'settings' => [
                'brand_name' => $brandName,
                'public_start_time' => $settings['public_start_time'] ?? null,
                'public_end_time' => $settings['public_end_time'] ?? null,
                'public_slot_minutes' => $settings['public_slot_minutes'] ?? null,
                'public_working_days' => $settings['public_working_days'] ?? null,
            ],
            'services' => $services,
            'professionals' => $professionals,
        ]);
    }
}
