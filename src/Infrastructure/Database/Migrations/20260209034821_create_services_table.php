<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;


final class CreateServicesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('services')
            ->addColumn('tenant_id', 'integer')
            ->addColumn('company_id', 'integer')
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('price', 'decimal', [
                'precision' => 10,
                'scale' => 2
            ])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('duration_minutes', 'text', ['null' => true])
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])

            ->addIndex(['company_id'])
            ->addIndex(['active'])
            ->create();
    }
}
