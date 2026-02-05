<?php
namespace Domain\Clients\Services;

use Domain\Clients\Entities\ClientEntity;
use Domain\Clients\Repositories\ClientRepository;

class ClientService {
  public function __construct(private readonly ClientRepository $repository) {}
  
  public function register(ClientEntity $client): bool
  {
    return $this->repository->register($client);
  } 

  public function update(ClientEntity $client): bool
  {
    return $this->repository->update($client);
  }

  public function delete(int $id): bool
  {
    return $this->repository->delete($id);
  }

  public function findAll(): array
  {
    return $this->repository->findAll();
  } 
  public function findById(int $id): ?ClientRepository
  {
    return $this->repository->findById($id);
  }
}