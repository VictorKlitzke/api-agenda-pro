<?php
namespace App\Domain\Clients\Services;

use App\Domain\Clients\Data\DTOs\Request\ClientRequest;
use App\Domain\Clients\Entities\ClientEntity;
use App\Domain\Clients\Repositories\ClientRepository;

class ClientService
{
  public function __construct(private readonly ClientRepository $repository)
  {
  }

  public function register(ClientRequest $client): bool
  {
    $client = ClientEntity::create(
      name: $client->name,
      phone: $client->phone,
      origem: $client->origem
    );
    return $this->repository->register(client: $client);
  }

  public function update(ClientRequest $client, int $id): bool
  {
    $clientEntity = ClientEntity::restore(
      id: $id,
      name: $client->name,
      phone: $client->phone,
      origem: $client->origem
    );
    return $this->repository->update(client: $clientEntity);
  }

  public function delete(int $id): bool
  {
    return $this->repository->delete($id);
  }

  public function findAll(): array
  {
    return $this->repository->findAll();
  }

  public function findAllByCompanyId(int $companyId): array
  {
    return $this->repository->findAllByCompanyId($companyId);
  }
  public function findById(int $id): ?ClientEntity
  {
    return $this->repository->findById($id);
  }
}