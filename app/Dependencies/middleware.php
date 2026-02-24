<?php

declare(strict_types=1);

use App\Application\Middleware\CorsMiddleware;
use App\Application\Middleware\AccessLogMiddleware;
use App\Application\Middleware\RateLimitMiddleware;
use App\Application\Middleware\RequestIdMiddleware;
use App\Application\Middleware\SessionMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(AccessLogMiddleware::class);
    $app->add(RateLimitMiddleware::class);
    $app->add(CorsMiddleware::class);
    $app->add(SessionMiddleware::class);
    $app->add(RequestIdMiddleware::class);
};
