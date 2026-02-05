<?php
declare(strict_types=1);

namespace App\Domain\Auth\Repositories;

use Illuminate\Database\Connection;

final class LoginAttemptRepository
{
    public function __construct(private Connection $connection) {}

    public function recordFailure(string $email): void
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $row = $this->connection->table('login_attempts')->where('email', $email)->first();

        if (!$row) {
            $this->connection->table('login_attempts')->insert([
                'email' => $email,
                'attempts' => 1,
                'last_attempt_at' => $now,
                'locked_until' => null,
            ]);
            return;
        }

        $attempts = (int) $row->attempts + 1;
        $lockedUntil = null;

        if ($attempts > 5) {
            $lockedUntil = (new \DateTimeImmutable('+15 minutes'))->format('Y-m-d H:i:s');
        }

        $this->connection->table('login_attempts')
            ->where('email', $email)
            ->update([
                'attempts' => $attempts,
                'last_attempt_at' => $now,
                'locked_until' => $lockedUntil,
            ]);
    }

    public function resetAttempts(string $email): void
    {
        $this->connection->table('login_attempts')->where('email', $email)->delete();
    }

    public function isLocked(string $email): bool
    {
        $row = $this->connection->table('login_attempts')->where('email', $email)->first();
        if (!$row) {
            return false;
        }

        if (!$row->locked_until) {
            return false;
        }

        $lockedUntil = new \DateTimeImmutable($row->locked_until);
        return $lockedUntil > new \DateTimeImmutable();
    }

    public function remainingAttempts(string $email): int
    {
        $row = $this->connection->table('login_attempts')->where('email', $email)->first();
        if (!$row) {
            return 5;
        }
        $attempts = (int) $row->attempts;
        return max(0, 5 - $attempts);
    }
}
