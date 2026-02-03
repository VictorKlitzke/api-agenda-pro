<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Company;

use App\Domain\Company\Entities\CompanyEntity;
use App\Domain\Company\Interfaces\CompanyInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCompanyRepository implements CompanyInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function save(CompanyEntity $company): CompanyEntity
    {
        $orm = new CompanyOrm(
            userId: $company->userId(),
            name: $company->name(),
            cnpj: $company->cnpj(),
            address: $company->address(),
            city: $company->city(),
            state: $company->state()
        );

        $this->em->persist($orm);
        $this->em->flush();

        return CompanyEntity::restore(
            id: $orm->getId(),
            userId: $orm->getUserId(),
            name: $orm->getName(),
            cnpj: $orm->getCnpj(),
            address: $orm->getAddress(),
            city: $orm->getCity(),
            state: $orm->getState(),
            active: $orm->isActive(),
            createdAt: $orm->getCreatedAt(),
            updatedAt: $orm->getUpdatedAt()
        );
    }

    public function update(CompanyEntity $company): CompanyEntity
    {
        $orm = $this->em->find(CompanyOrm::class, $company->id());

        if (!$orm) {
            return $company;
        }

        $orm->setName($company->name());
        $orm->setCnpj($company->cnpj());
        $orm->setAddress($company->address());
        $orm->setCity($company->city());
        $orm->setState($company->state());
        $orm->setUpdatedAt($company->updatedAt());

        $this->em->flush();

        return CompanyEntity::restore(
            id: $orm->getId(),
            userId: $orm->getUserId(),
            name: $orm->getName(),
            cnpj: $orm->getCnpj(),
            address: $orm->getAddress(),
            city: $orm->getCity(),
            state: $orm->getState(),
            active: $orm->isActive(),
            createdAt: $orm->getCreatedAt(),
            updatedAt: $orm->getUpdatedAt()
        );
    }

    public function findById(int $id): ?CompanyEntity
    {
        $orm = $this->em->find(CompanyOrm::class, $id);

        return $orm ? $this->mapToEntity($orm) : null;
    }

    public function findByUserId(int $userId): ?CompanyEntity
    {
        $orm = $this->em->getRepository(CompanyOrm::class)->findOneBy(['userId' => $userId]);

        return $orm ? $this->mapToEntity($orm) : null;
    }

    public function findAll(): array
    {
        $orms = $this->em->getRepository(CompanyOrm::class)->findAll();

        return array_map(fn(CompanyOrm $orm) => $this->mapToEntity($orm), $orms);
    }

    public function updateStatus(int $id, bool $active): void
    {
        $orm = $this->em->find(CompanyOrm::class, $id);

        if (!$orm) {
            return;
        }

        $orm->setActive($active);
        $orm->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }

    private function mapToEntity(CompanyOrm $orm): CompanyEntity
    {
        return CompanyEntity::restore(
            id: $orm->getId(),
            userId: $orm->getUserId(),
            name: $orm->getName(),
            cnpj: $orm->getCnpj(),
            address: $orm->getAddress(),
            city: $orm->getCity(),
            state: $orm->getState(),
            active: $orm->isActive(),
            createdAt: $orm->getCreatedAt(),
            updatedAt: $orm->getUpdatedAt()
        );
    }
}