<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRolePermissionsTable extends AbstractMigration
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
        $table = $this->table('role_permissions', [
            'id' => false,
            'primary_key' => ['role_id', 'permission_id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table
            ->addColumn('role_id', 'integer', ['null' => false])
            ->addColumn('permission_id', 'integer', ['null' => false])
            ->addIndex(['role_id'])
            ->addIndex(['permission_id'])
            ->create();
    }
}
