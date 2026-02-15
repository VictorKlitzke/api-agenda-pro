<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPublicAvailabilityToSettingsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('settings');
        $table
            ->addColumn('public_start_time', 'time', ['null' => true, 'after' => 'email_from_address'])
            ->addColumn('public_end_time', 'time', ['null' => true, 'after' => 'public_start_time'])
            ->addColumn('public_slot_minutes', 'integer', ['null' => true, 'after' => 'public_end_time'])
            ->addColumn('public_working_days', 'string', ['limit' => 50, 'null' => true, 'after' => 'public_slot_minutes'])
            ->update();
    }
}
