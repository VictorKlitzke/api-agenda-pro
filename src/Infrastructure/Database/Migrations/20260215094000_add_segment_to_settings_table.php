<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSegmentToSettingsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('settings');
        $table
            ->addColumn('segment', 'string', ['limit' => 40, 'null' => true, 'after' => 'email_from_address'])
            ->update();
    }
}
