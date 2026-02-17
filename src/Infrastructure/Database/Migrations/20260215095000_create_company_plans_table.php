<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCompanyPlansTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('company_plans')
            ->addColumn('company_id', 'integer')
            ->addColumn('plan_code', 'string', ['limit' => 50])
            ->addColumn('status', 'string', ['limit' => 30])
            ->addColumn('stripe_customer_id', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('stripe_subscription_id', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('current_period_end', 'datetime', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->addIndex(['company_id'], ['unique' => true])
            ->create();
    }
}
