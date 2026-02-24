<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications\WhatsApp;

use App\Domain\Shared\Interfaces\WhatsappNotifierInterface;

final class InfobipWhatsappNotifier implements WhatsappNotifierInterface
{
    public function __construct(
        private string $baseUrl,
        private string $apiKey,
        private string $sender,
        private string $defaultCountryCode = '55',
        private string $callbackData = ''
    ) {
    }

    public function sendText(string $phone, string $message): bool
    {
        $normalizedPhone = $this->normalizePhone($phone);
        $normalizedMessage = trim($message);
        $url = $this->buildEndpoint();

        if ($normalizedPhone === '' || $normalizedMessage === '' || $url === '') {
            return false;
        }

        $payload = [
            'from' => $this->sender,
            'to' => $normalizedPhone,
            'content' => [
                'text' => $normalizedMessage,
            ],
        ];

        if (trim($this->callbackData) !== '') {
            $payload['callbackData'] = $this->callbackData;
        }

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($body === false) {
            return false;
        }

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: App ' . $this->apiKey,
        ];

        if (function_exists('curl_init')) {
            return $this->sendViaCurl($url, $headers, $body);
        }

        return $this->sendViaStreamContext($url, $headers, $body);
    }

    private function buildEndpoint(): string
    {
        $base = trim($this->baseUrl);
        if ($base === '' || trim($this->apiKey) === '' || trim($this->sender) === '') {
            return '';
        }

        if (!str_starts_with($base, 'http://') && !str_starts_with($base, 'https://')) {
            $base = 'https://' . $base;
        }

        return rtrim($base, '/') . '/whatsapp/1/message/text';
    }

    private function sendViaCurl(string $url, array $headers, string $body): bool
    {
        $ch = curl_init($url);
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

    private function sendViaStreamContext(string $url, array $headers, string $body): bool
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

        $result = @file_get_contents($url, false, $context);
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
