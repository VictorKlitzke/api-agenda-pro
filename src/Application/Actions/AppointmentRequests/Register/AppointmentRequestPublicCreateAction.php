<?php

declare(strict_types=1);

namespace App\Application\Actions\AppointmentRequests\Register;

use App\Application\Actions\Action;
use App\Domain\Agendamentos\Services\AppointmentRequestService;
use App\Domain\Agendamentos\Services\AgendamentoService;
use App\Domain\Clients\Repositories\ClientRepository;
use App\Domain\Settings\Repositories\SettingsRepository;
use Psr\Http\Message\ResponseInterface;

final class AppointmentRequestPublicCreateAction extends Action
{
    public function __construct(
        private readonly AppointmentRequestService $service,
        private readonly AgendamentoService $agendamentos,
        private readonly ClientRepository $clients,
        private readonly SettingsRepository $settings
    ) {}

    public function action(): ResponseInterface
    {
        $data = (array) $this->request->getParsedBody();
        $companyId = (int) ($data['companyId'] ?? $data['company_id'] ?? 0);
        $clientName = trim((string) ($data['clientName'] ?? $data['client_name'] ?? ''));
        $preferredDate = (string) ($data['preferredDate'] ?? $data['preferred_date'] ?? '');
        $preferredTime = (string) ($data['preferredTime'] ?? $data['preferred_time'] ?? '');

        if (!$companyId || $clientName === '' || $preferredDate === '' || $preferredTime === '') {
            return $this->respondWithData([
                'message' => 'Dados inválidos',
            ], 422);
        }

        $clientPhone = (string) ($data['clientPhone'] ?? $data['client_phone'] ?? '');
        if ($clientPhone === '') {
            return $this->respondWithData([
                'message' => 'Telefone é obrigatório',
            ], 422);
        }
        $durationMinutes = (int) ($data['durationMinutes'] ?? $data['duration_minutes'] ?? 30);
        $settings = $this->settings->findByCompanyId($companyId) ?? [];
        $workingDaysRaw = (string) ($settings['public_working_days'] ?? '1,2,3,4,5');
        $workingDays = array_filter(array_map('intval', array_map('trim', explode(',', $workingDaysRaw))));
        $dayOfWeek = (int) (new \DateTimeImmutable($preferredDate))->format('N');
        if (!in_array($dayOfWeek, $workingDays, true)) {
            return $this->respondWithData([
                'message' => 'Dia indisponível',
            ], 409);
        }

        $startAt = sprintf('%s %s:00', $preferredDate, $preferredTime);
        $endAt = (new \DateTimeImmutable($startAt))
            ->modify(sprintf('+%d minutes', max(1, $durationMinutes)))
            ->format('Y-m-d H:i:s');

        $startTime = (string) ($settings['public_start_time'] ?? '09:00:00');
        $endTime = (string) ($settings['public_end_time'] ?? '18:00:00');
        $windowStart = new \DateTimeImmutable($preferredDate . ' ' . $startTime);
        $windowEnd = new \DateTimeImmutable($preferredDate . ' ' . $endTime);
        $requestedStart = new \DateTimeImmutable($startAt);
        $requestedEnd = new \DateTimeImmutable($endAt);

        if ($requestedStart < $windowStart || $requestedEnd > $windowEnd) {
            return $this->respondWithData([
                'message' => 'Horário fora do período disponível',
            ], 409);
        }

        if ($this->agendamentos->hasConflictByCompany($companyId, $startAt, $endAt)) {
            return $this->respondWithData([
                'message' => 'Horário indisponível',
            ], 409);
        }

        $existingClient = $this->clients->findByPhoneAndCompany($clientPhone, $companyId);
        $clientId = $existingClient ? (int) ($existingClient['id'] ?? 0) : $this->clients->registerForCompany(
            name: $clientName,
            phone: $clientPhone,
            origem: 'PUBLIC_REQUEST',
            companyId: $companyId
        );
        if (!$clientId) {
            return $this->respondWithData([
                'message' => 'Falha ao cadastrar cliente',
            ], 500);
        }

        $payload = [
            'company_id' => $companyId,
            'client_id' => $clientId ?: null,
            'client_name' => $clientName,
            'client_email' => $data['clientEmail'] ?? $data['client_email'] ?? null,
            'client_phone' => $clientPhone,
            'service_id' => isset($data['serviceId']) ? (int) $data['serviceId'] : (isset($data['service_id']) ? (int) $data['service_id'] : null),
            'preferred_date' => $preferredDate,
            'preferred_time' => $preferredTime,
            'notes' => $data['notes'] ?? null,
            'status' => 'PENDING',
        ];

        $created = $this->service->create($payload);

        return $this->respondWithData($created, 201);
    }
}
