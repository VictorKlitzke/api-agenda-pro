<?php 
namespace App\Domain\Permissions\Interfaces;


interface PermissionInterface {

public function findAll(): array;
public function findByKeys(array $keys): array;
}