<?php

declare(strict_types=1);

namespace App\Application\Actions\AppointmentRequests\Public;

use App\Application\Actions\Action;
use App\Domain\Agendamentos\Services\AgendamentoService;
use App\Domain\Settings\Repositories\SettingsRepository;
use Psr\Http\Message\ResponseInterface;

final class AppointmentRequestPublicAvailabilityAction extends Action
{
    public function __construct(
        private readonly SettingsRepository $settings,
        private readonly AgendamentoService $agendamentos
    ) {}

    public function action(): ResponseInterface
    {
        $params = $this->request->getQueryParams();
        $companyId = (int) ($params['companyId'] ?? $params['company_id'] ?? 0);
        $date = (string) ($params['date'] ?? '');

        if (!$companyId || $date === '') {
            return $this->respondWithData([], 400);
        }

        $settings = $this->settings->findByCompanyId($companyId) ?? [];
        $startTime = (string) ($settings['public_start_time'] ?? '09:00:00');
        $endTime = (string) ($settings['public_end_time'] ?? '18:00:00');
        $slotMinutes = (int) ($settings['public_slot_minutes'] ?? 30);
        $workingDaysRaw = (string) ($settings['public_working_days'] ?? '1,2,3,4,5');
        $workingDays = array_filter(array_map('intval', array_map('trim', explode(',', $workingDaysRaw))));

        $dayOfWeek = (int) (new \DateTimeImmutable($date))->format('N');
        if (!in_array($dayOfWeek, $workingDays, true)) {
            return $this->respondWithData([]);
        }

        $startAt = new \DateTimeImmutable($date . ' ' . $startTime);
        $endAt = new \DateTimeImmutable($date . ' ' . $endTime);

        $slots = [];
        $cursor = $startAt;
        $minutes = max(1, $slotMinutes);
        while ($cursor < $endAt) {
            $slotStart = $cursor;
            $slotEnd = $slotStart->modify(sprintf('+%d minutes', $minutes));
            if ($slotEnd > $endAt) {
                break;
            }
            $startStr = $slotStart->format('Y-m-d H:i:s');
            $endStr = $slotEnd->format('Y-m-d H:i:s');
            if (!$this->agendamentos->hasConflictByCompany($companyId, $startStr, $endStr)) {
                $slots[] = $slotStart->format('H:i');
            }
            $cursor = $slotEnd;
        }

        return $this->respondWithData($slots);
    }
}
