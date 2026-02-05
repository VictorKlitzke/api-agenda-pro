<?php
declare(strict_types=1);

namespace App\Domain\Auth\Repositories;

use Illuminate\Database\Connection;

final class UserTokenRepository
{
    public function __construct(private Connection $connection) {}

    public function createToken(int $userId, string $token, \DateTimeImmutable $expiresAt): void
    {
        $this->connection->table('user_tokens')->insert([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);
    }

    public function findUserByToken(string $token): ?int
    {
        $row = $this->connection->table('user_tokens')->where('token', $token)
            ->where('expires_at', '>', (new \DateTimeImmutable())->format('Y-m-d H:i:s'))
            ->first();

        return $row ? (int)$row->user_id : null;
    }
}
