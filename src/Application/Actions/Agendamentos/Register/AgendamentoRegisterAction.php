<?php 

namespace App\Application\Actions\Agendamentos\Register;

use App\Application\Actions\Action;
use App\Domain\Agendamentos\Data\DTOs\Request\AgendamentoRequest;
use App\Domain\Agendamentos\Services\AgendamentoService;

final class AgendamentoRegisterAction extends Action
{
    public function __construct(private readonly AgendamentoService $service){}

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $data = (array) $this->request->getParsedBody();
        $request = AgendamentoRequest::fromArray($data);
        $created = $this->service->register($request);
        $payload = $this->mapToResponse($created);

        return $this->respondWithData($payload, 201);
    }

    private function mapToResponse(array $row): array
    {
        $startAt = (string) ($row['start_at'] ?? '');
        $duration = (int) ($row['duration_minutes'] ?? 0);
        $endAt = $startAt !== '' ? (new \DateTimeImmutable($startAt))->modify("+{$duration} minutes")->format('Y-m-d H:i:s') : null;

        return [
            'id' => (int) ($row['id'] ?? 0),
            'companyId' => (int) ($row['company_id'] ?? 0),
            'professionalId' => (int) ($row['professional_id'] ?? 0),
            'clientId' => (int) ($row['client_id'] ?? 0),
            'serviceId' => (int) ($row['service_id'] ?? 0),
            'startAt' => $startAt,
            'endAt' => $endAt,
            'durationMinutes' => $duration,
            'notes' => $row['notes'] ?? null,
            'active' => isset($row['active']) ? (bool) $row['active'] : null,
        ];
    }
}
