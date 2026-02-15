<?php

declare(strict_types=1);

namespace App\Domain\Settings\Repositories;

use Illuminate\Database\Connection;

final class SettingsRepository
{
    public function __construct(private Connection $connection) {}

    public function findByCompanyId(int $companyId): ?array
    {
        $row = $this->connection->table('settings')
            ->where('company_id', $companyId)
            ->first();

        return $row ? (array) $row : null;
    }

    public function upsert(int $companyId, array $data): array
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $payload = array_merge($data, [
            'company_id' => $companyId,
            'updated_at' => $now,
        ]);

        $exists = $this->findByCompanyId($companyId);
        if ($exists) {
            $this->connection->table('settings')
                ->where('company_id', $companyId)
                ->update($payload);
        } else {
            $payload['created_at'] = $now;
            $this->connection->table('settings')->insert($payload);
        }

        return $this->findByCompanyId($companyId) ?? [];
    }
}
