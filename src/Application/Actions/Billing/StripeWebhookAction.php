<?php

declare(strict_types=1);

namespace App\Application\Actions\Billing;

use App\Application\Actions\Action;
use App\Domain\CompanyPlan\Services\CompanyPlanService;
use Stripe\StripeClient;
use Stripe\Webhook;

final class StripeWebhookAction extends Action
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        private CompanyPlanService $companyPlans
    ) {
        parent::__construct($logger);
    }

    protected function action(): \Psr\Http\Message\ResponseInterface
    {
        $payload   = (string) $this->request->getBody();
        $sigHeader = $this->request->getHeaderLine('Stripe-Signature');
        $secret    = (string) ($_ENV['STRIPE_WEBHOOK_SECRET'] ?? '');

        if ($secret === '') {
            return $this->respondWithData(['message' => 'Webhook não configurado'], 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Throwable $e) {
            return $this->respondWithData(['message' => 'Assinatura inválida'], 400);
        }

        $type = (string) ($event->type ?? '');

        $this->logger->info('Stripe webhook received', ['type' => $type]);

        match ($type) {
            'checkout.session.completed'    => $this->handleCheckoutCompleted($event->data->object),
            'invoice.paid'                  => $this->handleInvoicePaid($event->data->object),
            'invoice.payment_failed'        => $this->handleInvoicePaymentFailed($event->data->object),
            'customer.subscription.updated' => $this->handleSubscriptionEvent($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionEvent($event->data->object),
            default                         => null,
        };

        return $this->respondWithData(['received' => true]);
    }
    private function handleCheckoutCompleted(object $session): void
    {
        $companyId      = (int)    ($session->metadata->company_id ?? $session->client_reference_id ?? 0);
        $plan           = (string) ($session->metadata->plan       ?? '');
        $subscriptionId = (string) ($session->subscription         ?? '');
        $customerId     = (string) ($session->customer             ?? '');

        $this->logger->info('checkout.session.completed', [
            'company_id'      => $companyId,
            'plan'            => $plan,
            'subscription_id' => $subscriptionId,
            'payment_status'  => $session->payment_status ?? 'unknown',
        ]);

        if ($companyId <= 0 || $plan === '') {
            $this->logger->warning('checkout.session.completed: company_id ou plan ausente nos metadados');
            return;
        }

        $this->companyPlans->upsert($companyId, [
            'plan_code'              => $plan,
            'status'                 => 'pending',
            'stripe_customer_id'     => $customerId     ?: null,
            'stripe_subscription_id' => $subscriptionId ?: null,
            'current_period_end'     => null,
        ]);
    }
    private function handleInvoicePaid(object $invoice): void
    {
        $subscriptionId = (string) ($invoice->subscription ?? '');
        $customerId     = (string) ($invoice->customer     ?? '');

        if ($subscriptionId === '') {
            $this->logger->info('invoice.paid: sem subscription_id, ignorando');
            return;
        }

        $subscription = $this->retrieveSubscription($subscriptionId);
        if ($subscription === null) {
            return;
        }

        $companyId = (int)    ($subscription->metadata->company_id ?? 0);
        $plan      = (string) ($subscription->metadata->plan       ?? '');

        $this->logger->info('invoice.paid', [
            'company_id'      => $companyId,
            'plan'            => $plan,
            'subscription_id' => $subscriptionId,
        ]);

        if ($companyId <= 0 || $plan === '') {
            $this->logger->warning('invoice.paid: company_id ou plan ausente nos metadados da subscription. Adicione subscription_data.metadata ao criar a Checkout Session.');
            return;
        }

        $periodEnd = isset($subscription->current_period_end)
            ? (new \DateTimeImmutable())->setTimestamp((int) $subscription->current_period_end)
            : null;

        $this->companyPlans->upsert($companyId, [
            'plan_code'              => $plan,
            'status'                 => (string) ($subscription->status ?? 'active'),
            'stripe_customer_id'     => $customerId ?: null,
            'stripe_subscription_id' => $subscriptionId,
            'current_period_end'     => $periodEnd?->format('Y-m-d H:i:s'),
        ]);
    }

    private function handleInvoicePaymentFailed(object $invoice): void
    {
        $subscriptionId = (string) ($invoice->subscription ?? '');
        $customerId     = (string) ($invoice->customer     ?? '');

        if ($subscriptionId === '') {
            return;
        }

        $subscription = $this->retrieveSubscription($subscriptionId);
        if ($subscription === null) {
            return;
        }

        $companyId = (int)    ($subscription->metadata->company_id ?? 0);
        $plan      = (string) ($subscription->metadata->plan       ?? '');

        $this->logger->warning('invoice.payment_failed', [
            'company_id'      => $companyId,
            'subscription_id' => $subscriptionId,
        ]);

        if ($companyId <= 0 || $plan === '') {
            return;
        }

        $this->companyPlans->upsert($companyId, [
            'plan_code'              => $plan,
            'status'                 => 'past_due',
            'stripe_customer_id'     => $customerId ?: null,
            'stripe_subscription_id' => $subscriptionId,
            'current_period_end'     => null,
        ]);
    }

    private function handleSubscriptionEvent(object $subscription): void
    {
        $companyId      = (int)    ($subscription->metadata->company_id ?? 0);
        $plan           = (string) ($subscription->metadata->plan       ?? '');
        $subscriptionId = (string) ($subscription->id                   ?? '');
        $customerId     = (string) ($subscription->customer             ?? '');
        $status         = (string) ($subscription->status               ?? '');

        $this->logger->info('customer.subscription event', [
            'company_id'      => $companyId,
            'plan'            => $plan,
            'subscription_id' => $subscriptionId,
            'status'          => $status,
        ]);

        if ($companyId <= 0 || $plan === '') {
            $this->logger->warning('customer.subscription: company_id ou plan ausente nos metadados. Adicione subscription_data.metadata ao criar a Checkout Session.');
            return;
        }

        $periodEnd = isset($subscription->current_period_end)
            ? (new \DateTimeImmutable())->setTimestamp((int) $subscription->current_period_end)
            : null;

        $this->companyPlans->upsert($companyId, [
            'plan_code'              => $plan,
            'status'                 => $status,
            'stripe_customer_id'     => $customerId ?: null,
            'stripe_subscription_id' => $subscriptionId,
            'current_period_end'     => $periodEnd?->format('Y-m-d H:i:s'),
        ]);
    }
    private function retrieveSubscription(string $subscriptionId): ?object
    {
        $secret = (string) ($_ENV['STRIPE_SECRET_KEY'] ?? '');

        if ($secret === '') {
            $this->logger->error('STRIPE_SECRET_KEY não configurado');
            return null;
        }

        try {
            $stripe = new StripeClient($secret);
            return $stripe->subscriptions->retrieve($subscriptionId, []);
        } catch (\Throwable $e) {
            $this->logger->error('Stripe subscription retrieve error', [
                'message'         => $e->getMessage(),
                'subscription_id' => $subscriptionId,
            ]);
            return null;
        }
    }
}