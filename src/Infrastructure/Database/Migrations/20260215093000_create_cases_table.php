<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCasesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('cases', [
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('client_id', 'integer', ['null' => true])
            ->addColumn('professional_id', 'integer', ['null' => true])
            ->addColumn('title', 'string', ['limit' => 200])
            ->addColumn('case_number', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('area', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('status', 'string', ['limit' => 40, 'default' => 'Ativo'])
            ->addColumn('priority', 'string', ['limit' => 20, 'default' => 'Normal'])
            ->addColumn('notes', 'text', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['null' => true, 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['company_id'])
            ->addIndex(['client_id'])
            ->addIndex(['professional_id'])
            ->create();
    }
}
