<?php

declare(strict_types=1);

namespace App\Domain\Settings\Services;

use App\Domain\Settings\Repositories\SettingsRepository;

final class SettingsService
{
    private const FIELDS = [
        'brand_name',
        'primary_color',
        'secondary_color',
        'logo_url',
        'favicon_url',
        'custom_domain',
        'email_from_name',
        'email_from_address',
        'public_start_time',
        'public_end_time',
        'public_slot_minutes',
        'public_working_days',
        'segment',
    ];

    public function __construct(private SettingsRepository $settings) {}

    public function getByCompanyId(int $companyId): ?array
    {
        return $this->settings->findByCompanyId($companyId);
    }

    public function update(int $companyId, array $data): array
    {
        $filtered = [];
        foreach (self::FIELDS as $field) {
            if (array_key_exists($field, $data)) {
                $value = is_string($data[$field]) ? trim($data[$field]) : $data[$field];
                $filtered[$field] = $value === '' ? null : $value;
            }
        }

        return $this->settings->upsert($companyId, $filtered);
    }
}
