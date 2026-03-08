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
        private readonly string $defaultCountryCode,
    ) {}

    public function sendText(string $phone, string $message, array $metadata = []): bool
    {
        if (!$this->enabled) {
            $this->logger->warning('whatsapp_notifier_disabled', [
                'provider' => $this->provider,
                'endpoint' => $this->endpoint,
            ]);
            return false;
        }

        $normalizedPhone = $this->normalizePhone($phone);
        $trimmedMessage = trim($message);

        if ($normalizedPhone === '' || $trimmedMessage === '') {
            $this->logger->warning('whatsapp_invalid_payload', [
                'has_phone' => $normalizedPhone !== '',
                'has_message' => $trimmedMessage !== '',
            ]);
            return false;
        }

        $provider = strtolower(trim($this->provider));
        if (!in_array($provider, ['unofficial_api', 'generic_http', 'infobip', 'meta'], true)) {
            $this->logger->warning('whatsapp_provider_not_supported', [
                'provider' => $this->provider,
            ]);
        }

        if ($this->endpoint === '') {
            $this->logger->warning('whatsapp_endpoint_not_configured');
            return false;
        }

        $endpoint = $this->normalizeEndpoint($this->endpoint);

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

        $jsonPayload = (string) json_encode($payload, JSON_UNESCAPED_UNICODE);

        try {
            $httpResult = $this->sendViaStreams($endpoint, $headers, $jsonPayload);
            if ($httpResult['status'] === 0 && function_exists('curl_init')) {
                $httpResult = $this->sendViaCurl($endpoint, $headers, $jsonPayload);
            }

            $httpCode = (int) ($httpResult['status'] ?? 0);
            $responseBody = $httpResult['response'] ?? null;

            if ($httpCode >= 200 && $httpCode < 300) {
                return true;
            }

            $this->logger->warning('whatsapp_http_send_failed', [
                'status' => $httpCode,
                'response' => is_string($responseBody) ? $responseBody : null,
                'endpoint' => $endpoint,
                'provider' => $this->provider,
            ]);
            return false;
        } catch (\Throwable $exception) {
            $this->logger->error('whatsapp_http_send_exception', [
                'message' => $exception->getMessage(),
                'endpoint' => $endpoint,
            ]);
            return false;
        }
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?: '';
        if ($digits === '') {
            return '';
        }

        $countryCode = preg_replace('/\D+/', '', $this->defaultCountryCode) ?: '';
        if ($countryCode === '') {
            return $digits;
        }

        if (str_starts_with($digits, $countryCode)) {
            return $digits;
        }

        // BR common local formats without DDI: 10 or 11 digits (landline/mobile with area code).
        if (strlen($digits) === 10 || strlen($digits) === 11) {
            return $countryCode . $digits;
        }

        return $digits;
    }

    private function normalizeEndpoint(string $endpoint): string
    {
        $trimmed = trim($endpoint);
        if ($trimmed === '') {
            return '';
        }

        $parts = parse_url($trimmed);
        $path = $parts['path'] ?? '';

        if ($path === '' || $path === '/') {
            return rtrim($trimmed, '/') . '/send-text';
        }

        return $trimmed;
    }

    /**
     * @param list<string> $headers
     * @return array{status:int,response:?string}
     */
    private function sendViaStreams(string $endpoint, array $headers, string $jsonPayload): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $jsonPayload,
                'timeout' => $this->timeoutSeconds,
                'ignore_errors' => true,
            ],
        ]);

        $result = @file_get_contents($endpoint, false, $context);
        $httpCode = 0;

        if (isset($http_response_header) && is_array($http_response_header) && isset($http_response_header[0])) {
            if (preg_match('/\s(\d{3})\s/', (string) $http_response_header[0], $matches)) {
                $httpCode = (int) ($matches[1] ?? 0);
            }
        }

        return [
            'status' => $httpCode,
            'response' => is_string($result) ? $result : null,
        ];
    }

    /**
     * @param list<string> $headers
     * @return array{status:int,response:?string}
     */
    private function sendViaCurl(string $endpoint, array $headers, string $jsonPayload): array
    {
        $ch = curl_init($endpoint);
        if ($ch === false) {
            return [
                'status' => 0,
                'response' => null,
            ];
        }

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutSeconds);

        $result = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $status,
            'response' => is_string($result) ? $result : null,
        ];
    }
}
