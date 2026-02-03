<?php

declare(strict_types=1);

namespace App\Domain\Company\Services;

use App\Domain\Company\Data\DTOs\Request\RegisterCompanyRequest;
use App\Domain\Company\Data\DTOs\Request\UpdateCompanyRequest;
use App\Domain\Company\Entities\CompanyEntity;
use App\Domain\Company\Interfaces\CompanyInterface;
use App\Domain\DomainException\DomainRecordNotFoundException;

final class CompanyService
{
    public function __construct(
        private CompanyInterface $companies
    ) {
    }

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

        $company->changeName($request->name());
        $company->changeCnpj($request->cnpj());
        $company->changeAddress($request->address(), $request->city(), $request->state());

        return $this->companies->update($company);
    }

    public function deactivate(int $id): void
    {
        $company = $this->companies->findById($id);

        if (!$company) {
            throw new DomainRecordNotFoundException('Empresa não encontrada');
        }

        $company->deactivate();
        $this->companies->updateStatus($id, false);
    }

    public function listAll(): array
    {
        return $this->companies->findAll();
    }

    public function findById(int $id): ?CompanyEntity
    {
        return $this->companies->findById($id);
    }

    public function findByUserId(int $userId): ?CompanyEntity
    {
        return $this->companies->findByUserId($userId);
    }
}
