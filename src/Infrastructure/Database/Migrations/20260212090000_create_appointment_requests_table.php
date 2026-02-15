<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAppointmentRequestsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('appointment_requests', [
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table
            ->addColumn('company_id', 'integer', ['null' => false])
            ->addColumn('client_name', 'string', ['limit' => 150])
            ->addColumn('client_email', 'string', ['limit' => 150, 'null' => true])
            ->addColumn('client_phone', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('service_id', 'integer', ['null' => true])
            ->addColumn('preferred_date', 'date', ['null' => false])
            ->addColumn('preferred_time', 'time', ['null' => false])
            ->addColumn('notes', 'text', ['null' => true])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'PENDING'])
            ->addColumn('appointment_id', 'integer', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['null' => true, 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['company_id'])
            ->addIndex(['status'])
            ->addIndex(['appointment_id'])
            ->create();
    }
}
