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

    public function save(ServiceEntity $service, array $products = []): ServiceEntity
    {
        $this->connection->beginTransaction();
        try {
            $id = $this->connection->table('services')->insertGetId([
                'company_id' => $service->getCompanyId(),
                'name' => $service->getServiceName(),
                'description' => $service->getDescription(),
                'price' => $service->getPrice(),
                'duration_minutes' => $service->getDuration(),
                'active' => $service->isActive() ? 1 : 0,
            ]);

            if (is_array($products) && count($products) > 0) {
                foreach ($products as $productId) {
                    $this->connection->table('services_products')->insert([
                        'tenant_id' => $service->getCompanyId(),
                        'service_id' => $id,
                        'product_id' => $productId,
                        'quantity' => 1,
                    ]);
                }
            }

            $this->connection->commit();
            return $this->findById((int) $id);
        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function update(ServiceEntity $service, int $id, array $products = []): bool
    {

        $updated = $this->connection->table('services')
            ->where('id', $id)
            ->update([
                'company_id' => $service->getCompanyId(),
                'name' => $service->getServiceName(),
                'description' => $service->getDescription(),
                'price' => $service->getPrice(),
                'duration_minutes' => $service->getDuration(),
                'active' => $service->isActive() ? 1 : 0,
            ]) > 0;

        if (is_array($products)) {
            $this->connection->table('services_products')->where('service_id', $id)->delete();
            foreach ($products as $productId) {
                $this->connection->table('services_products')->insert([
                    'tenant_id' => $service->getCompanyId(),
                    'service_id' => $id,
                    'product_id' => $productId,
                    'quantity' => 1,
                ]);
            }
        }

        return $updated;

    }

    public function findAll(): array
    {
        $rows = $this->connection->table('services')
            ->leftJoin('services_products', 'services_products.service_id', '=', 'services.id')
            ->leftJoin('products', 'products.id', '=', 'services_products.product_id')
            ->select(
                'services.id as id',
                'services.tenant_id as company_id',
                'services.name as name',
                'services.description as description',
                'services.price as price',
                'services.duration_minutes as duration_minutes',
                'services.active as active',
                'services_products.product_id as product_id',
                'products.name as product_name'
            )
            ->get()->toArray();

        $grouped = [];
        foreach ($rows as $r) {
            $row = (array) $r;
            $id = (int) $row['id'];
            if (!isset($grouped[$id])) {
                $grouped[$id] = [
                    'id' => $id,
                    'company_id' => (int) ($row['company_id'] ?? 0),
                    'name' => (string) ($row['name'] ?? ''),
                    'description' => isset($row['description']) ? (string) $row['description'] : null,
                    'price' => isset($row['price']) ? (float) $row['price'] : null,
                    'duration_minutes' => isset($row['duration_minutes']) ? (string) $row['duration_minutes'] : '',
                    'active' => isset($row['active']) ? (bool) $row['active'] : true,
                    'products' => [],
                ];
            }

            if (!empty($row['product_id'])) {
                $grouped[$id]['products'][] = [
                    'id' => (int) $row['product_id'],
                    'name' => (string) ($row['product_name'] ?? ''),
                ];
            }
        }

        return array_values($grouped);
    }

    public function findAllByCompanyId(int $companyId): array
    {
        $rows = $this->connection->table('services')
            ->leftJoin('services_products', 'services_products.service_id', '=', 'services.id')
            ->leftJoin('products', 'products.id', '=', 'services_products.product_id')
            ->where('services.company_id', $companyId)
            ->select(
                'services.id as id',
                'services.company_id as company_id',
                'services.name as name',
                'services.description as description',
                'services.price as price',
                'services.duration_minutes as duration_minutes',
                'services.active as active',
                'services_products.product_id as product_id',
                'products.name as product_name'
            )
            ->get()->toArray();

        $grouped = [];
        foreach ($rows as $r) {
            $row = (array) $r;
            $id = (int) $row['id'];
            if (!isset($grouped[$id])) {
                $grouped[$id] = [
                    'id' => $id,
                    'company_id' => (int) ($row['company_id'] ?? 0),
                    'name' => (string) ($row['name'] ?? ''),
                    'description' => isset($row['description']) ? (string) $row['description'] : null,
                    'price' => isset($row['price']) ? (float) $row['price'] : null,
                    'duration_minutes' => isset($row['duration_minutes']) ? (string) $row['duration_minutes'] : '',
                    'active' => isset($row['active']) ? (bool) $row['active'] : true,
                    'products' => [],
                ];
            }

            if (!empty($row['product_id'])) {
                $grouped[$id]['products'][] = [
                    'id' => (int) $row['product_id'],
                    'name' => (string) ($row['product_name'] ?? ''),
                ];
            }
        }

        return array_values($grouped);
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
            (int) ($data['company_id'] ?? $data['tenant_id'] ?? 0),
            (string) $data['name'],
            (float) $data['price'],
            isset($data['description']) ? (string) $data['description'] : null,
            (string) ($data['duration_minutes'] ?? $data['duration'] ?? ''),
            (bool) ($data['active'] ?? true),
        );
    }
}