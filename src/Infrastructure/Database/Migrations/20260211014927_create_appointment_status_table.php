<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAppointmentStatusTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('appointment_status', [
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table
            ->addColumn('appointment_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('status', 'string', [
                'limit' => 30,
                'null' => false,
            ])
            ->addColumn('changed_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('note', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addIndex(['appointment_id'])
            ->create();
    }
}
