<?php

declare(strict_types=1);

namespace App\Application\Actions\Billing;

use App\Application\Actions\Action;
use Stripe\StripeClient;

final class CreateCheckoutSessionAction extends Action
{
    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $body = (array) $this->request->getParsedBody();

        $plan = strtolower(trim((string) ($body['plan'] ?? '')));
        $companyId = (int) ($body['companyId'] ?? $body['company_id'] ?? 0);

        $priceMap = [
            'basic' => (string) ($_ENV['STRIPE_PRICE_BASIC'] ?? ''),
            'medium' => (string) ($_ENV['STRIPE_PRICE_MEDIUM'] ?? ''),
            'advanced' => (string) ($_ENV['STRIPE_PRICE_ADVANCED'] ?? ''),
        ];

        if ($companyId <= 0 || !isset($priceMap[$plan]) || $priceMap[$plan] === '') {
            return $this->respondWithData([
                'message' => 'Plano inválido',
            ], 422);
        }

        $secret = (string) ($_ENV['STRIPE_SECRET_KEY'] ?? '');
        if ($secret === '') {
            return $this->respondWithData([
                'message' => 'Stripe não configurado',
            ], 500);
        }

        $frontendUrl = rtrim((string) ($_ENV['FRONTEND_URL']), '/');

        try {
            $stripe = new StripeClient($secret);
            $session = $stripe->checkout->sessions->create([
                'mode' => 'subscription',
                'line_items' => [
                    [
                        'price' => $priceMap[$plan],
                        'quantity' => 1,
                    ],
                ],
                'subscription_data' => [
                    'metadata' => [
                        'company_id' => (string) $companyId,
                        'plan' => $plan,
                    ],
                ],
                'success_url' => $frontendUrl . '/dashboard?success=1',
                'cancel_url' => $frontendUrl . '/planos?canceled=1',
                'client_reference_id' => (string) $companyId,
                'metadata' => [
                    'company_id' => (string) $companyId,
                    'plan' => $plan,
                ],
            ]);

            return $this->respondWithData([
                'url' => $session->url,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Stripe checkout error', [
                'message' => $e->getMessage(),
                'plan' => $plan,
                'company_id' => $companyId,
            ]);
            return $this->respondWithData([
                'message' => $e->getMessage() ?: 'Falha ao criar sessão de checkout',
            ], 500);
        }
    }
}
