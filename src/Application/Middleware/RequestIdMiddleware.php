<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class RequestIdMiddleware implements Middleware
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $incomingRequestId = trim($request->getHeaderLine('X-Request-Id'));
        $requestId = $incomingRequestId !== '' ? $incomingRequestId : $this->generateRequestId();

        $_SERVER['APP_REQUEST_ID'] = $requestId;
        $request = $request->withAttribute('requestId', $requestId);

        $response = $handler->handle($request);

        return $response->withHeader('X-Request-Id', $requestId);
    }

    private function generateRequestId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
