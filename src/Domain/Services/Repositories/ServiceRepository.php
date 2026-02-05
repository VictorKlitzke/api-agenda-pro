<?php 
namespace App\Domain\Services\Repositories;

use App\Domain\Services\Interfaces\ServiceInterface;

class ServiceRepository implements ServiceInterface
{
    public function __construct(protected Connection $connection)
    {
    }

    public function save(ServiceEntity $service): ServiceEntity
    {
        $id = $this->connection->table('services')->insertGetId([
            'company_id' => $service->companyId(),
            'name' => $service->name(),
            'description' => $service->description(),
            'price' => $service->price(),
            'duration_minutes' => $service->durationMinutes(),
            'active' => $service->isActive() ? 1 : 0,
            'created_at' => $service->createdAt()->format('Y-m-d H:i:s'),
        ]);

        return $this->findById((int) $id);
    }
    public function findById(int $id): ServiceEntity
    {
        $row = $this->connection->table('services')->where('id', $id)->first();

        return $row ? $this->mapToEntity((array) $row) : null;
    }
    public function findByIds(array $ids): ServiceEntity
    {
        $rows = $this->connection->table('services')->whereIn('id', $ids)->get();

        $result = [];
        foreach ($rows as $r) {
            $result[] = $this->mapToEntity((array) $r);
        }
        return $result;
    }
    public function find(int $id): ServiceEntity
    {
        $row = $this->connection->table('services')->where('id', $id)->first();

        return $row ? $this->mapToEntity((array) $row) : null;
    }

    public function mapToEntity(array $data): ServiceEntity
    {
        return ServiceEntity::restore(
            (int)$data['id'],
            (int)$data['company_id'],
            (string)$data['name'],
            (string)$data['description'],
            (float)$data['price'],
            (int)$data['duration_minutes'],
            (bool)($data['active'] ?? true),
            new \DateTimeImmutable($data['created_at'])
        );
    }   
}