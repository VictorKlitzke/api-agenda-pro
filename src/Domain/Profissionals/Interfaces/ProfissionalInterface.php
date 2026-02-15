<?php 
namespace App\Domain\Profissionals\Interfaces;

use App\Domain\Profissionals\Entities\ProfissionalEntity;


interface ProfissionalInterface {
    public function register(ProfissionalEntity $profissional): bool;
    public function update(ProfissionalEntity $profissional): bool;
    public function delete(ProfissionalEntity $profissional): bool;
    public function findAll(): array;
    public function findAllByCompanyId(int $companyId): array;
    public function find(int $id): ?ProfissionalEntity;
    public function active(int $id, string $status): bool;

}