<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateLoginAttemptsAndTokens extends AbstractMigration
{
    public function change(): void
    {
        if (!$this->hasTable('login_attempts')) {
            $table = $this->table('login_attempts', ['id' => false, 'primary_key' => ['email']]);
            $table->addColumn('email', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('attempts', 'integer', ['default' => 0, 'null' => false])
                ->addColumn('last_attempt_at', 'datetime', ['null' => true])
                ->addColumn('locked_until', 'datetime', ['null' => true])
                ->create();
        }

        if (!$this->hasTable('user_tokens')) {
            $table = $this->table('user_tokens');
            $table->addColumn('user_id', 'integer')
                ->addColumn('token', 'string', ['limit' => 255])
                ->addColumn('expires_at', 'datetime')
                ->addColumn('created_at', 'datetime')
                ->addIndex(['token'], ['unique' => true])
                ->create();
        }
    }
}
