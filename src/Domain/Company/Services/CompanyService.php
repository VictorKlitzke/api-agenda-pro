<?php

declare(strict_types=1);

namespace App\Domain\Company\Services;

use App\Domain\Company\Data\DTOs\Request\RegisterCompanyRequest;
use App\Domain\Company\Data\DTOs\Request\UpdateCompanyRequest;
use App\Domain\Company\Entities\CompanyEntity;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\DomainException\DomainRecordNotFoundException;

final class CompanyService
{
    public function __construct(
        private CompanyRepository $companies
    ) {}

    public function register(RegisterCompanyRequest $request): CompanyEntity
    {
        $company = CompanyEntity::create(
            userId: $request->userId(),
            name: $request->name(),
            cnpj: $request->cnpj(),
            address: $request->address(),
            city: $request->city(),
            state: $request->state()
        );

        return $this->companies->save($company);
    }

    public function update(UpdateCompanyRequest $request): CompanyEntity
    {
        $company = $this->companies->findById($request->id());

        if (!$company) {
            throw new DomainRecordNotFoundException('Empresa não encontrada');
        }

        $company->changeName(name: $request->name());
        $company->changeCnpj(cnpj: $request->cnpj());
        $company->changeAddress(address: $request->address(), city: $request->city(), state: $request->state());

        return $this->companies->update($company);
    }

    public function deactivate(int $id): void
    {
        $company = $this->companies->findById($id);

        if (!$company) {
            throw new DomainRecordNotFoundException('Empresa não encontrada');
        }

        $company->deactivate();
        $this->companies->updateStatus(id: $id, active: false);
    }

    public function listAll(): array
    {
        return $this->companies->findAll();
    }

    public function findById(int $id): ?CompanyEntity
    {
        return $this->companies->findById(id: $id);
    }

    public function findByUserId(int $userId): ?int
    {
        $res = $this->companies->findByUserId(userId: $userId);
        return $res;
    }

    public function findEntityByUserId(int $userId): array
    {
        return $this->companies->findEntityByUserId(userId: $userId);
    }
}
