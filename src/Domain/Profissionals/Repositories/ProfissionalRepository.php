<?php 
namespace App\Domain\Profissionals\Repositories;

use App\Domain\Profissionals\Entities\ProfissionalEntity;
use App\Domain\Profissionals\Interfaces\ProfissionalInterface;
use Illuminate\Database\Connection;

final class ProfissionalRepository implements ProfissionalInterface
{
    public function __construct(protected Connection $connection)
    {}

    public function getCompanyId(int $id): ?int
    {
        $row = $this->connection->table('profissionals')
            ->where('id', $id)
            ->first();

        return $row ? (int) $row->company_id : null;
    }

    public function register(ProfissionalEntity $profissional): bool {

        return $this->connection->table('profissionals')->insert([
            'company_id' => $profissional->getCompanyId(),
            'name' => $profissional->getName(),
            'email' => $profissional->getEmail(),
            'active' => 1,
            'specialty' => $profissional->getSpecialty(),
            'phone' => $profissional->getPhone(),
        ]) > 0;

    }

    public function update(ProfissionalEntity $profissional): bool {

        return $this->connection->table('profissionals')
            ->where('id', $profissional->getId())
            ->update([
                'company_id' => $profissional->getCompanyId(),
                'name' => $profissional->getName(),
                'email' => $profissional->getEmail(),
                'specialty' => $profissional->getSpecialty(),
                'phone' => $profissional->getPhone(),
            ]) > 0;

    }

    public function delete(ProfissionalEntity $profissional): bool {

        return $this->connection->table('profissionals')
            ->where('id', $profissional->getId())
            ->delete() > 0;

    }   

    public function findAll(): array {

        return $this->connection->table('profissionals')->get()->toArray(); 
    }

    public function findAllByCompanyId(int $companyId): array
    {
        return $this->connection->table('profissionals')
            ->where('company_id', $companyId)
            ->get()
            ->toArray();
    }

    public function find(int $id): ?ProfissionalEntity {

        $row = $this->connection->table('profissionals')
            ->where('id', $id)
            ->first();

        if (!$row) {
            return null;
        }

        return $this->mapToEntity((array) $row);

    }

    public function active(int $id, string $status): bool {
        return $this->connection->table('profissionals')
            ->where('id', $id)
            ->update([
                'active' => $status,
            ]) > 0;
    }

    private function mapToEntity(array $data): ProfissionalEntity
    {
        return ProfissionalEntity::restore(
            id: (int) $data['id'],
            companyId: (int) $data['company_id'],
            name: (string) $data['name'],
            email: (string) $data['email'],
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            specialty: isset($data['specialty']) ? (string) $data['specialty'] : null,
            createdAt: new \DateTimeImmutable($data['created_at']),
            updatedAt: isset($data['updated_at']) ? new \DateTimeImmutable($data['updated_at']) : null,
        );
    }

}