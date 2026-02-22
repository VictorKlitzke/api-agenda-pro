<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPreferredProfessionalToAppointmentRequests extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('appointment_requests');
        if (!$table->hasColumn('preferred_professional_id')) {
            $table->addColumn('preferred_professional_id', 'integer', ['null' => true, 'default' => null])
                ->update();
        }
    }
}
