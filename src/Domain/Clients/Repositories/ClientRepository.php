<?php
namespace Domain\Clients\Repositories;  

use Domain\Clients\Data\DTOs\Request\ClientInterface;
use Domain\Clients\Entities\ClientEntity;
use Illuminate\Database\Connection;


class ClientRepository implements ClientInterface
{

    public function __construct(
        protected Connection $connection,
    ) {}

    public function register(ClientEntity $client): bool
    {
        return $this->connection->table('clients')->insert([
            'name' => $client->getName(),
            'phone' => $client->getPhone(),
            'origem' => $client->getOrigem(),
        ]) > 0;
    }

    public function update(ClientEntity $client): bool
    {
        return $this->connection->table('clients')
            ->where('id', $client->getId())
            ->update([
                'name' => $client->getName(),
                'phone' => $client->getPhone(),
                'origem' => $client->getOrigem(),
            ]) > 0;
    }

    public function delete(int $id): bool
    {
        return $this->connection->table('clients')
            ->where('id', $id)
            ->delete() > 0;
    }

    public function findAll(): array
    {
        return $this->connection->table('clients')->get()->toArray();
    }

    public function findById(int $id): ?self
    {
        $client = $this->connection->table('clients')
            ->where('id', $id)
            ->first();

        if (!$client) {
            return null;
        }

        return new self($this->connection);
    }


}