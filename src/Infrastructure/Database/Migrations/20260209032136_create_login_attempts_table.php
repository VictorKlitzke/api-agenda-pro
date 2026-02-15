<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateLoginAttemptsTable extends AbstractMigration
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

        if (!$this->hasTable('login_attempts')) {
            $table = $this->table('login_attempts', ['id' => false, 'primary_key' => ['email']]);
            $table->addColumn('email', 'string', ['limit' => 255])
                ->addColumn('attempts', 'integer', ['default' => 0])
                ->addColumn('last_attempt_at', 'datetime', ['null' => true])
                ->addColumn('locked_until', 'datetime', ['null' => true])
                ->create();
        }

    }
}