<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProfissionalTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('profissionals', [
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table
            ->addColumn('company_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'limit' => 150,
                'null' => false,
            ])
            ->addColumn('email', 'string', [
                'limit' => 150,
                'null' => true,
            ])
            ->addColumn('phone', 'string', [
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('specialty', 'string', [
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('active', 'boolean', [
                'default' => 1,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('updated_at', 'timestamp', [
                'null' => true,
                'update' => 'CURRENT_TIMESTAMP',
            ])
            ->addIndex(['company_id'])
            ->addIndex(['company_id', 'active'])
            ->addIndex(['email'])
            ->create();
    }
}
