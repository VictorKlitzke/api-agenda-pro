<?php

declare(strict_types=1);

namespace App\Infrastructure\Whatsapp;

use App\Domain\Shared\Interfaces\WhatsappNotifierInterface;
use Psr\Log\LoggerInterface;

final class HttpWhatsappNotifier implements WhatsappNotifierInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly bool $enabled,
        private readonly string $provider,
        private readonly string $endpoint,
        private readonly string $phoneField,
        private readonly string $messageField,
        private readonly string $token,
        private readonly string $tokenHeader,
        private readonly string $tokenPrefix,
        private readonly array $extraPayload,
        private readonly int $timeoutSeconds,
    ) {}

    public function sendText(string $phone, string $message, array $metadata = []): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $normalizedPhone = preg_replace('/\D+/', '', $phone) ?: '';
        $trimmedMessage = trim($message);

        if ($normalizedPhone === '' || $trimmedMessage === '') {
            return false;
        }

        if ($this->provider !== 'unofficial_api') {
            $this->logger->warning('whatsapp_provider_not_supported', [
                'provider' => $this->provider,
            ]);
            return false;
        }

        if ($this->endpoint === '') {
            $this->logger->warning('whatsapp_endpoint_not_configured');
            return false;
        }

        $payload = $this->extraPayload;
        $payload[$this->phoneField] = $normalizedPhone;
        $payload[$this->messageField] = $trimmedMessage;

        if (isset($metadata['companyId']) && !isset($payload['companyId'])) {
            $payload['companyId'] = (int) $metadata['companyId'];
        }

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        if ($this->token !== '') {
            $value = $this->tokenPrefix !== '' ? ($this->tokenPrefix . ' ' . $this->token) : $this->token;
            $headers[] = $this->tokenHeader . ': ' . $value;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => (string) json_encode($payload, JSON_UNESCAPED_UNICODE),
                'timeout' => $this->timeoutSeconds,
                'ignore_errors' => true,
            ],
        ]);

        try {
            $result = @file_get_contents($this->endpoint, false, $context);

            $httpCode = 0;
            if (isset($http_response_header) && is_array($http_response_header) && isset($http_response_header[0])) {
                if (preg_match('/\s(\d{3})\s/', (string) $http_response_header[0], $matches)) {
                    $httpCode = (int) ($matches[1] ?? 0);
                }
            }

            if ($httpCode >= 200 && $httpCode < 300) {
                return true;
            }

            $this->logger->warning('whatsapp_http_send_failed', [
                'status' => $httpCode,
                'response' => is_string($result) ? $result : null,
            ]);
            return false;
        } catch (\Throwable $exception) {
            $this->logger->error('whatsapp_http_send_exception', [
                'message' => $exception->getMessage(),
            ]);
            return false;
        }
    }
}
