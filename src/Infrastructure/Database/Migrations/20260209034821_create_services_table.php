<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;


final class CreateServicesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('services')
            ->addColumn('tenant_id', 'integer')
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('price', 'decimal', [
                'precision' => 10,
                'scale' => 2
            ])
            ->addColumn('observation', 'text', ['null' => true])
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])

            ->addIndex(['tenant_id'])
            ->addIndex(['active'])
            ->create();
    }
}
