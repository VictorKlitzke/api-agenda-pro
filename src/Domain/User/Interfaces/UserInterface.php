<?php 
namespace App\Domain\User\Interfaces;

use App\Domain\User\Entities\UserEntity;

interface UserInterface
{
    public function findByEmail(string $email): ?UserEntity;
    public function findById(int $id): ?UserEntity;
    public function save(UserEntity $user, ?string $verificationCode = null): void;
    public function verifyEmailCode(string $email, string $verificationCode): bool;
}