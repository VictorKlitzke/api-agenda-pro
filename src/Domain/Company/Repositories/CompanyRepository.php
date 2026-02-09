<?php
namespace App\Domain\Company\Repositories;
use App\Domain\Company\Entities\CompanyEntity;
use App\Domain\Company\Interfaces\CompanyInterface;
use Illuminate\Database\Connection;

class CompanyRepository implements CompanyInterface
{

    public function __construct(protected Connection $connection) {}

    public function save(CompanyEntity $company): CompanyEntity
    {
        $id = $this->connection->table('companies')->insertGetId([
            'user_id' => $company->userId(),
            'name' => $company->name(),
            'cnpj' => $company->cnpj(),
            'address' => $company->address(),
            'city' => $company->city(),
            'state' => $company->state(),
            'active' => $company->isActive() ? 1 : 0,
            'created_at' => $company->createdAt()->format('Y-m-d H:i:s'),
        ]);

        return $this->findById((int) $id);
    }

    public function update(CompanyEntity $company): CompanyEntity
    {
        $this->connection->table('companies')
            ->where('id', $company->id())
            ->update([
                'name' => $company->name(),
                'cnpj' => $company->cnpj(),
                'address' => $company->address(),
                'city' => $company->city(),
                'state' => $company->state(),
                'updated_at' => $company->updatedAt()?->format('Y-m-d H:i:s'),
            ]);

        return $this->findById($company->id());
    }

    public function findById(int $id): ?CompanyEntity
    {
        $row = $this->connection->table('companies')->where('id', $id)->first();

        return $row ? $this->mapToEntity((array) $row) : null;
    }

    public function findByUserId(int $userId): ?CompanyEntity
    {
        $row = $this->connection->table('companies')->where('user_id', $userId)->first();
        return $row ? $this->mapToEntity((array) $row) : null;
    }

    public function findAll(): array
    {
        $rows = $this->connection->table('companies')->get();

        $result = [];
        foreach ($rows as $r) {
            $result[] = $this->mapToEntity((array) $r);
        }

        return $result;
    }

    public function updateStatus(int $id, bool $active): void
    {
        $this->connection->table('companies')->where('id', $id)->update([
            'active' => $active ? 1 : 0,
            'updated_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);
    }

    private function mapToEntity(array $row): CompanyEntity
    {
        return CompanyEntity::restore(
            (int) $row['id'],
            (int) $row['user_id'],
            (string) $row['name'],
            (string) ($row['cnpj'] ?? ''),
            (string) ($row['address'] ?? ''),
            (string) ($row['city'] ?? ''),
            (string) ($row['state'] ?? ''),
            (bool) ($row['active'] ?? true),
            new \DateTimeImmutable($row['created_at']),
            isset($row['updated_at']) && $row['updated_at'] ? new \DateTimeImmutable($row['updated_at']) : null
        );
    }
}