<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateStockMovementsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('stock_movements');
        $table
            ->addColumn('company_id', 'integer')
            ->addColumn('stock_id', 'integer')
            ->addColumn('quantity', 'integer')
            ->addColumn('movement_type', 'string', ['limit' => 3])
            ->addColumn('notes', 'text', ['null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['company_id'])
            ->addIndex(['stock_id'])
            ->addIndex(['movement_type'])
            ->addIndex(['created_at'])
            ->create();
    }
}
