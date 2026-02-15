<?php

declare(strict_types=1);

namespace App\Application\Actions\Notifications\List;

use App\Application\Actions\Action;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\Agendamentos\Services\AppointmentRequestService;
use App\Domain\Products\Services\ProductService;
use Psr\Http\Message\ResponseInterface;

final class NotificationListAction extends Action
{
    private const LOW_STOCK_THRESHOLD = 10;

    public function __construct(
        private readonly ProductService $productService,
        private readonly CompanyRepository $companies,
        private readonly AppointmentRequestService $appointmentRequests
    ) {}

    public function action(): ResponseInterface
    {
        $userId = (int) ($this->request->getAttribute('userId') ?? 0);
        $companyId = $this->companies->findByUserId($userId);
        if (!$companyId) {
            return $this->respondWithData([]);
        }

        $products = $this->productService->findAllByCompanyId($companyId);
        $requests = $this->appointmentRequests->listByCompanyId($companyId, 'PENDING');
        $now = (new \DateTimeImmutable())->format('c');

        $notifications = [];
        foreach ($products as $product) {
            $quantity = isset($product->quantity) ? (int) $product->quantity : 0;
            if ($quantity < self::LOW_STOCK_THRESHOLD) {
                $productId = isset($product->id) ? (int) $product->id : 0;
                $productName = isset($product->name) ? (string) $product->name : 'Produto';
                $notifications[] = [
                    'id' => 'low-stock-' . $productId,
                    'type' => 'LOW_STOCK',
                    'title' => 'Estoque baixo',
                    'description' => sprintf('%s com %d unidade(s)', $productName, $quantity),
                    'createdAt' => $now,
                    'meta' => [
                        'productId' => $productId,
                        'quantity' => $quantity,
                    ],
                ];
            }
      }

        foreach ($requests as $request) {
            $requestId = (int) ($request['id'] ?? 0);
            $clientName = (string) ($request['client_name'] ?? 'Cliente');
            $preferredDate = (string) ($request['preferred_date'] ?? '');
            $preferredTime = (string) ($request['preferred_time'] ?? '');
            $description = $preferredDate && $preferredTime
                ? sprintf('%s solicitou para %s %s', $clientName, $preferredDate, $preferredTime)
                : sprintf('%s solicitou um agendamento', $clientName);

            $notifications[] = [
                'id' => 'appointment-request-' . $requestId,
                'type' => 'APPOINTMENT_REQUEST',
                'title' => 'Solicitação de agendamento',
                'description' => $description,
                'createdAt' => $request['created_at'] ?? $now,
                'meta' => [
                    'requestId' => $requestId,
                ],
            ];
        }

        return $this->respondWithData($notifications);
    }
}
