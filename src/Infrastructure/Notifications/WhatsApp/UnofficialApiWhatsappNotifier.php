<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications\WhatsApp;

use App\Domain\Shared\Interfaces\WhatsappNotifierInterface;

final class UnofficialApiWhatsappNotifier implements WhatsappNotifierInterface
{
    public function __construct(
        private string $endpoint,
        private string $token = '',
        private string $tokenHeader = 'Authorization',
        private string $tokenPrefix = 'Bearer',
        private string $phoneField = 'number',
        private string $messageField = 'text',
        private array $extraPayload = [],
        private string $defaultCountryCode = '55'
    ) {
    }

    public function sendText(string $phone, string $message): bool
    {
        $normalizedPhone = $this->normalizePhone($phone);
        if ($normalizedPhone === '' || trim($message) === '' || trim($this->endpoint) === '') {
            return false;
        }

        $payload = array_merge($this->extraPayload, [
            $this->phoneField => $normalizedPhone,
            $this->messageField => $message,
        ]);

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($body === false) {
            return false;
        }

        $headers = ['Content-Type: application/json'];
        if (trim($this->token) !== '') {
            $value = trim($this->tokenPrefix) !== ''
                ? trim($this->tokenPrefix) . ' ' . trim($this->token)
                : trim($this->token);
            $headers[] = trim($this->tokenHeader) . ': ' . $value;
        }

        if (function_exists('curl_init')) {
            return $this->sendViaCurl($headers, $body);
        }

        return $this->sendViaStreamContext($headers, $body);
    }

    private function sendViaCurl(array $headers, string $body): bool
    {
        $ch = curl_init($this->endpoint);
        if ($ch === false) {
            return false;
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_TIMEOUT => 8,
        ]);

        curl_exec($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $statusCode >= 200 && $statusCode < 300;
    }

    private function sendViaStreamContext(array $headers, string $body): bool
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $body,
                'ignore_errors' => true,
                'timeout' => 8,
            ],
        ]);

        $result = @file_get_contents($this->endpoint, false, $context);
        if ($result === false || !isset($http_response_header) || !is_array($http_response_header)) {
            return false;
        }

        $statusLine = $http_response_header[0] ?? '';
        if (!preg_match('/\s(\d{3})\s/', $statusLine, $matches)) {
            return false;
        }

        $statusCode = (int) $matches[1];
        return $statusCode >= 200 && $statusCode < 300;
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        $countryCode = preg_replace('/\D+/', '', $this->defaultCountryCode) ?: '55';

        if (!str_starts_with($digits, $countryCode)) {
            $digits = $countryCode . ltrim($digits, '0');
        }

        return $digits;
    }
}
