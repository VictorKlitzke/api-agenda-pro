<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Repositories;

use Illuminate\Database\Connection;

final class DashboardRepository
{
    public function __construct(private Connection $connection) {}

    public function countAppointmentsToday(int $companyId, string $today): int
    {
        return (int) $this->connection->table('appointments')
            ->join('appointment_status', 'appointments.id', '=', 'appointment_status.appointment_id')
            ->where('appointments.company_id', $companyId)
            ->whereRaw('DATE(appointments.start_at) = ?', [$today])
            ->where('appointment_status.status', '!=', 'Cancelado')
            ->count();
    }

    public function countNewClientsToday(int $companyId, string $today): int
    {
        return (int) $this->connection->table('clients')
            ->where('company_id', $companyId)
            ->whereRaw('DATE(created_at) = ?', [$today])
            ->count();
    }

    public function getLowStockProducts(int $companyId, int $threshold, int $limit = 10): array
    {
        return $this->connection->table('products')
            ->select('id', 'name', 'quantity')
            ->where('company_id', $companyId)
            ->where('quantity', '<=', $threshold)
            ->orderBy('quantity', 'asc')
            ->limit($limit)
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();
    }

    public function getTopServices(int $companyId, int $days, int $limit = 5): array
    {
        $startDate = (new \DateTimeImmutable())->modify("-{$days} days")->format('Y-m-d');

        return $this->connection->table('appointments')
            ->join('appointment_status', 'appointments.id', '=', 'appointment_status.appointment_id')
            ->join('services', 'services.id', '=', 'appointments.service_id')
            ->where('appointments.company_id', $companyId)
            ->whereRaw('DATE(appointments.start_at) >= ?', [$startDate])
            ->where('appointment_status.status', '!=', 'Cancelado')
            ->groupBy('appointments.service_id', 'services.name')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->select([
                'appointments.service_id as service_id',
                'services.name as service_name',
            ])
            ->selectRaw('COUNT(*) as total')
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();
    }

    public function getCancellationRate(int $companyId, int $days): array
    {
        $startDate = (new \DateTimeImmutable())->modify("-{$days} days")->format('Y-m-d');

        $total = (int) $this->connection->table('appointments')
            ->join('appointment_status', 'appointments.id', '=', 'appointment_status.appointment_id')
            ->where('appointments.company_id', $companyId)
            ->whereRaw('DATE(appointments.start_at) >= ?', [$startDate])
            ->count();

        $canceled = (int) $this->connection->table('appointments')
            ->join('appointment_status', 'appointments.id', '=', 'appointment_status.appointment_id')
            ->where('appointments.company_id', $companyId)
            ->whereRaw('DATE(appointments.start_at) >= ?', [$startDate])
            ->where('appointment_status.status', 'Cancelado')
            ->count();

        $rate = $total > 0 ? round(($canceled / $total) * 100, 2) : 0.0;

        return [
            'total' => $total,
            'canceled' => $canceled,
            'rate' => $rate,
        ];
    }
}
