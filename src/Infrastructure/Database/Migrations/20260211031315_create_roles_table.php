<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRolesTable extends AbstractMigration
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

        $table = $this->table('roles', [
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('name', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('description', 'string', [
                'limit' => 150,
                'null' => true,
            ])
            ->addColumn('is_default', 'boolean', [
                'default' => 0,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addIndex(['company_id'])
            ->addIndex(['company_id', 'name'], ['unique' => true])
            ->create();

    }
}
