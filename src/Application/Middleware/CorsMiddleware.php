<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Settings\SettingsInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as SlimResponse;

class CorsMiddleware
{
    private array $allowedOrigins;
    private bool $allowCredentials;
    private string $allowedHeaders;
    private string $allowedMethods;

    public function __construct(SettingsInterface $settings)
    {
        $cors = $settings->get('cors') ?? [];
        $this->allowedOrigins = $cors['allowed_origins'] ?? [];
        $this->allowCredentials = (bool)($cors['allow_credentials'] ?? false);
        $this->allowedHeaders = $cors['allowed_headers'] ?? 'X-Requested-With, Content-Type, Accept, Origin, Authorization';
        $this->allowedMethods = $cors['allowed_methods'] ?? 'GET, POST, PUT, PATCH, DELETE, OPTIONS';
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            $response = new SlimResponse();
            return $this->withCorsHeaders($response, $this->resolveOrigin($request));
        }

        $response = $handler->handle($request);
        return $this->withCorsHeaders($response, $this->resolveOrigin($request));
    }

    private function resolveOrigin(Request $request): ?string
    {
        $origin = $request->getHeaderLine('Origin');
        if ($origin === '') {
            return null;
        }

        if ($this->isLocalDevelopmentOrigin($origin)) {
            return $origin;
        }

        if (empty($this->allowedOrigins) || in_array('*', $this->allowedOrigins, true)) {
            return '*';
        }

        if (in_array($origin, $this->allowedOrigins, true)) {
            return $origin;
        }

        return null;
    }

    private function isLocalDevelopmentOrigin(string $origin): bool
    {
        $appEnv = strtolower((string) ($_ENV['APP_ENV'] ?? ''));
        if ($appEnv !== 'local') {
            return false;
        }

        $parts = parse_url($origin);
        if (!is_array($parts)) {
            return false;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        if ($host === 'localhost' || $host === '127.0.0.1') {
            return true;
        }

        return str_starts_with($host, '192.168.');
    }

    private function withCorsHeaders(Response $response, ?string $origin): Response
    {
        if ($origin === null) {
            return $response;
        }

        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Headers', $this->allowedHeaders)
            ->withHeader('Access-Control-Allow-Methods', $this->allowedMethods);

        if ($origin !== '*' && $this->allowCredentials) {
            $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        if ($origin !== '*') {
            $response = $response->withHeader('Vary', 'Origin');
        }

        return $response;
    }
}