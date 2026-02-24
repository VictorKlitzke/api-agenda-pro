<?php

declare(strict_types=1);

namespace Tests\Application\Middleware;

use App\Application\Middleware\AuthMiddleware;
use App\Domain\Auth\Repositories\UserTokenRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;

final class AuthMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        $_ENV['AUTH_ALLOW_BEARER_FALLBACK'] = 'false';
        $_SESSION = [];
    }

    public function testAllowsAuthenticatedSessionToken(): void
    {
        $_ENV['AUTH_ALLOW_BEARER_FALLBACK'] = 'false';

        $_SESSION['access_token'] = 'session-token';

        $tokens = $this->createMock(UserTokenRepository::class);
        $tokens->expects(self::once())
            ->method('findUserByToken')
            ->with('session-token')
            ->willReturn(10);

        $middleware = new AuthMiddleware($tokens);
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/companies');

        $response = $middleware->process($request, new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                TestCase::assertSame(10, $request->getAttribute('userId'));
                return new Response(200);
            }
        });

        self::assertSame(200, $response->getStatusCode());
    }

    public function testRejectsBearerHeaderWhenFallbackIsDisabled(): void
    {
        $_ENV['AUTH_ALLOW_BEARER_FALLBACK'] = 'false';

        $_SESSION = [];

        $tokens = $this->createMock(UserTokenRepository::class);
        $tokens->expects(self::never())->method('findUserByToken');

        $middleware = new AuthMiddleware($tokens);
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/companies')
            ->withHeader('Authorization', 'Bearer legacy-token');

        $this->expectException(HttpUnauthorizedException::class);
        $middleware->process($request, new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(200);
            }
        });
    }

    public function testAcceptsBearerHeaderWhenFallbackIsEnabled(): void
    {
        $_ENV['AUTH_ALLOW_BEARER_FALLBACK'] = 'true';

        $_SESSION = [];

        $tokens = $this->createMock(UserTokenRepository::class);
        $tokens->expects(self::once())
            ->method('findUserByToken')
            ->with('legacy-token')
            ->willReturn(7);

        $middleware = new AuthMiddleware($tokens);
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/companies')
            ->withHeader('Authorization', 'Bearer legacy-token');

        $response = $middleware->process($request, new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                TestCase::assertSame(7, $request->getAttribute('userId'));
                return new Response(200);
            }
        });

        self::assertSame(200, $response->getStatusCode());
    }
}
