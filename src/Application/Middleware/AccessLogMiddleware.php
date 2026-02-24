<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;

final class AccessLogMiddleware implements Middleware
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $start = microtime(true);

        $response = $handler->handle($request);

        $durationMs = (int) round((microtime(true) - $start) * 1000);
        $requestId = (string) ($request->getAttribute('requestId') ?? $_SERVER['APP_REQUEST_ID'] ?? '');
        $route = $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();

        $this->logger->info('http_request', [
            'request_id' => $requestId,
            'method' => strtoupper($request->getMethod()),
            'path' => $query !== '' ? $route . '?' . $query : $route,
            'status' => $response->getStatusCode(),
            'duration_ms' => $durationMs,
            'ip' => $this->resolveClientIp($request),
        ]);

        return $response;
    }

    private function resolveClientIp(Request $request): string
    {
        $forwardedFor = $request->getHeaderLine('X-Forwarded-For');
        if ($forwardedFor !== '') {
            $parts = array_map('trim', explode(',', $forwardedFor));
            return $parts[0] ?? 'unknown';
        }

        $server = $request->getServerParams();
        return (string) ($server['REMOTE_ADDR'] ?? 'unknown');
    }
}
