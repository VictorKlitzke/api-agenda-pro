<?php 
namespace App\Domain\User\Repositories;
use App\Domain\User\Entities\UserEntity;
use App\Domain\User\Interfaces\UserInterface;
use Illuminate\Database\Connection;


class UserRepository implements UserInterface
{
    public function __construct(protected Connection $connection) {}

    public function findByEmail(string $email): ?UserEntity
    {
        $row = $this->connection->table('users')->where('email', $email)->first();

        if (!$row) {
            return null;
        }

        return UserEntity::restore(
            (int)$row->id,
            (string)$row->name,
            (string)$row->email,
            (string)$row->password,
            $row->tipo_conta ?? 'PF',
            $row->telefone ?? '',
            $row->zip_code ?? '',
            (bool)($row->active ?? true),
            new \DateTimeImmutable($row->created_at)
        );
    }

    public function findById(int $id): ?UserEntity
    {
        $row = $this->connection->table('users')->where('id', $id)->first();

        if (!$row) {
            return null;
        }

        return UserEntity::restore(
            (int)$row->id,
            (string)$row->name,
            (string)$row->email,
            (string)$row->password,
            $row->tipo_conta ?? 'PF',
            $row->telefone ?? '',
            $row->zip_code ?? '',
            (bool)($row->active ?? true),
            new \DateTimeImmutable($row->created_at)
        );
    }

    public function save(UserEntity $user, ?string $verificationCode = null): void
    {
        $data = [
            'name' => $user->name(),
            'email' => $user->email(),
            'password' => $user->passwordHash(),
            'tipo_conta' => $user->tipoConta(),
            'telefone' => $user->telefone(),
            'zip_code' => $user->zipCode(),
            'active' => 1,
            'created_at' => $user->createdAt()->format('Y-m-d H:i:s'),
        ];

        if ($verificationCode !== null) $data['verification_code'] = $verificationCode;
        $this->connection->table('users')->insert($data);
    }
}