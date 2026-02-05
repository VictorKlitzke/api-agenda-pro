<?php 

namespace Domain\Clients\Data\DTOs\Request;

use Domain\Clients\Entities\ClientEntity;

interface ClientInterface {
    public function register(ClientEntity $client): bool;
    public function delete(int $id): bool;
    public function update(ClientEntity $client): bool;
    public function findAll(): array;
    public function findById(int $id): ?self;
}