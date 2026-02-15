<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Settings\SettingsInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpTooManyRequestsException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

final class RateLimitMiddleware implements Middleware
{
    private int $maxRequests;
    private int $windowSeconds;
    private FilesystemAdapter $cache;

    public function __construct(SettingsInterface $settings)
    {
        $rate = $settings->get('rate_limit') ?? [];
        $this->maxRequests = (int) ($rate['max_requests'] ?? 60);
        $this->windowSeconds = (int) ($rate['window_seconds'] ?? 60);
        $this->cache = new FilesystemAdapter(
            namespace: 'rate_limit',
            defaultLifetime: $this->windowSeconds,
            directory: __DIR__ . '/../../../var/cache/rate-limit'
        );
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return $handler->handle($request);
        }

        $ip = $this->resolveClientIp($request);
        $key = 'ip_' . preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $ip);

        $item = $this->cache->getItem($key);
        $data = $item->isHit() ? (array) $item->get() : ['count' => 0, 'reset' => time() + $this->windowSeconds];

        if ($data['reset'] < time()) {
            $data = ['count' => 0, 'reset' => time() + $this->windowSeconds];
        }

        $data['count']++;
        $item->set($data);
        $item->expiresAfter($this->windowSeconds);
        $this->cache->save($item);

        if ($data['count'] > $this->maxRequests) {
            throw new HttpTooManyRequestsException($request, 'Muitas requisições. Tente novamente em instantes.');
        }

        $response = $handler->handle($request);

        return $response
            ->withHeader('X-RateLimit-Limit', (string) $this->maxRequests)
            ->withHeader('X-RateLimit-Remaining', (string) max(0, $this->maxRequests - $data['count']))
            ->withHeader('X-RateLimit-Reset', (string) $data['reset']);
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
