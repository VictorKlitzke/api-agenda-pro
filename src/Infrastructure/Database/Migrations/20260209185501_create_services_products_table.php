<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateServicesProductsTable extends AbstractMigration
{

    public function change(): void
    {

        $this->table('services_products')
            ->addColumn('tenant_id', 'integer')
            ->addColumn('service_id', 'integer')
            ->addColumn('product_id', 'integer')

            ->addColumn('quantity', 'integer', ['default' => 1])

            ->addIndex(['tenant_id'])
            ->addIndex(['service_id'])
            ->addIndex(['product_id'])

            ->addIndex(
                ['service_id', 'product_id'],
                ['unique' => true]
            )

            ->create();

    }
}
