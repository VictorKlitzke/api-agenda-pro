<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSettingsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('settings', [
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('brand_name', 'string', ['limit' => 120, 'null' => true])
            ->addColumn('primary_color', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('secondary_color', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('logo_url', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('favicon_url', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('custom_domain', 'string', ['limit' => 190, 'null' => true])
            ->addColumn('email_from_name', 'string', ['limit' => 120, 'null' => true])
            ->addColumn('email_from_address', 'string', ['limit' => 150, 'null' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['null' => true, 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['company_id'], ['unique' => true])
            ->addIndex(['custom_domain'], ['unique' => true])
            ->create();
    }
}
