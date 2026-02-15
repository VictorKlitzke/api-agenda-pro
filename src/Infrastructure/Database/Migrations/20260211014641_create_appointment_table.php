<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAppointmentTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('appointments', [
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table
            ->addColumn('company_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('professional_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('client_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('service_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('start_at', 'datetime', [
                'null' => false,
            ])
            ->addColumn('duration_minutes', 'integer', [
                'null' => false,
            ])
            ->addColumn('end_at', 'datetime', [
                'null' => false,
            ])
            ->addColumn('notes', 'text', [
                'null' => true,
            ])
            ->addColumn('active', 'boolean', [
                'default' => 1,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('updated_at', 'timestamp', [
                'null' => true,
                'update' => 'CURRENT_TIMESTAMP',
            ])

            // índices críticos
            ->addIndex(['company_id'])
            ->addIndex(['professional_id'])
            ->addIndex(['service_id'])
            ->addIndex(['start_at'])
            ->addIndex(['professional_id', 'start_at'])
            ->create();
    }
}
