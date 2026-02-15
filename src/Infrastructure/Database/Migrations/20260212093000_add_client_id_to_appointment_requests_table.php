<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddClientIdToAppointmentRequestsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('appointment_requests');
        $table
            ->addColumn('client_id', 'integer', ['null' => true, 'after' => 'company_id'])
            ->addIndex(['client_id'])
            ->update();
    }
}
