<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProductTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('products');

        $table
            ->addColumn('tenant_id', 'integer')
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('description', 'text', ['null' => true])

            ->addColumn('price', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => true
            ])

            // estoque
            ->addColumn('stock_quantity', 'integer', ['default' => 0])
            ->addColumn('company_id', 'integer')
            ->addColumn('active', 'boolean', ['default' => true])

            // auditoria mínima
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', [
                'null' => true,
                'update' => 'CURRENT_TIMESTAMP'
            ])

            // índices
            ->addIndex(['tenant_id'])
            ->addIndex(['active'])
            ->addIndex(['name'])

            ->create();
    }
}
