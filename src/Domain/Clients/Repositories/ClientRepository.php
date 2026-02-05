<?php

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
        return true;
    }

    public function delete(int $id): bool
    {
        return true;
    }

    public function findAll(): array
    {
        return [];
    }

    public function findById(int $id): ?self
    {
        return null;
    }


}