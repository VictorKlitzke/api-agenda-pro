<?php 
namespace App\Domain\Company\Interfaces;

use App\Domain\Company\Entities\CompanyEntity;

interface CompanyInterface
{
    public function save(CompanyEntity $company): CompanyEntity;
    public function update(CompanyEntity $company): CompanyEntity;
    public function findById(int $id): ?CompanyEntity;
    public function findByUserId(int $userId): ?CompanyEntity;
    /** @return CompanyEntity[] */
    public function findAll(): array;
    public function updateStatus(int $id, bool $active): void;
}