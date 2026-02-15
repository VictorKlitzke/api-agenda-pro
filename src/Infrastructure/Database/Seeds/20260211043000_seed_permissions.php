<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class SeedPermissions extends AbstractSeed
{
    public function run(): void
    {
        $rows = [
            ['key' => 'appointments.manage', 'description' => 'Gerenciar agendamentos'],
            ['key' => 'clients.manage', 'description' => 'Gerenciar clientes'],
            ['key' => 'services.manage', 'description' => 'Gerenciar serviços'],
            ['key' => 'products.manage', 'description' => 'Gerenciar produtos'],
            ['key' => 'stock.manage', 'description' => 'Gerenciar estoque'],
        ];

        foreach ($rows as $row) {
            $key = addslashes($row['key']);
            $desc = addslashes($row['description']);
            $this->execute(
                "INSERT IGNORE INTO permissions (`key`, `description`) VALUES ('$key', '$desc')"
            );
        }
    }
}
