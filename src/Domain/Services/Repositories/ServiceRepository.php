<?php
namespace App\Domain\Services\Repositories;

use App\Domain\Services\Entities\ServiceEntity;
use App\Domain\Services\Interfaces\ServiceInterface;
use Illuminate\Database\Connection;

class ServiceRepository implements ServiceInterface
{
    public function __construct(protected Connection $connection)
    {
    }

    public function save(ServiceEntity $service): ServiceEntity
    {
        $id = $this->connection->table('services')->insertGetId([
            'company_id' => $service->getCompanyId(),
            'name' => $service->getServiceName(),
            'description' => $service->getDescription(),
            'price' => $service->getPrice(),
            'duration_minutes' => $service->getDuration(),
            'active' => $service->isActive() ? 1 : 0,
        ]);

        return $this->findById((int) $id);
    }

    public function update(ServiceEntity $service): bool
    {
        return $this->connection->table('services')
            ->where('id', $service->getId())
            ->update([
                'company_id' => $service->getCompanyId(),
                'name' => $service->getServiceName(),
                'description' => $service->getDescription(),
                'price' => $service->getPrice(),
                'duration_minutes' => $service->getDuration(),
                'active' => $service->isActive() ? 1 : 0,
            ]) > 0;
    }

    public function findAll(): array
    {
        $rows = $this->connection->table('services')->get();

        $result = [];
        foreach ($rows as $r) {
            $result[] = $this->mapToEntity((array) $r);
        }
        return $result;
    }

    public function delete(int $id): bool
    {
        return $this->connection->table('services')
            ->where('id', $id)
            ->delete() > 0;
    }

    public function findById(int $id): ?ServiceEntity
    {
        $row = $this->connection->table('services')->where('id', $id)->first();

        return $row ? $this->mapToEntity((array) $row) : null;
    }

    public function mapToEntity(array $data): ServiceEntity
    {
        return ServiceEntity::restore(
            (int) $data['id'],
            (int) $data['company_id'],
            (string) $data['name'],
            (string) $data['description'],
            (float) $data['price'],
            (int) $data['duration_minutes'],
            (bool) ($data['active'] ?? true),
        );
    }
}