<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateClientsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('clients');

        $table
            ->addColumn('tenant_id', 'integer')
            ->addColumn('company_id', 'integer')
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('phone', 'string', ['limit' => 20])
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])

            ->addIndex(['tenant_id'])
            ->addIndex(['phone'])

            ->create();
    }
}
