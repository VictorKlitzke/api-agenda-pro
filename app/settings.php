<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            $debug = filter_var($_ENV['APP_DEBUG'] ?? 'false', FILTER_VALIDATE_BOOLEAN);

            $corsAllowedOrigins = array_values(array_filter(array_map(
                'trim',
                explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? '')
            )));

            return new Settings([
                'displayErrorDetails' => $debug,
                'logError' => filter_var($_ENV['APP_LOG_ERRORS'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
                'logErrorDetails' => filter_var($_ENV['APP_LOG_ERROR_DETAILS'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
                'rate_limit' => [
                    'max_requests' => (int) ($_ENV['RATE_LIMIT_MAX'] ?? 60),
                    'window_seconds' => (int) ($_ENV['RATE_LIMIT_WINDOW'] ?? 60),
                ],
                'cors' => [
                    'allowed_origins' => $corsAllowedOrigins,
                    'allow_credentials' => filter_var($_ENV['CORS_ALLOW_CREDENTIALS'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
                    'allowed_headers' => $_ENV['CORS_ALLOWED_HEADERS'] ?? 'X-Requested-With, Content-Type, Accept, Origin, Authorization',
                    'allowed_methods' => $_ENV['CORS_ALLOWED_METHODS'] ?? 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                ],
                'mail' => [
                    'from' => $_ENV['MAIL_FROM'] ?? null,
                    'smtp_host' => $_ENV['MAIL_SMTP_HOST'] ?? null,
                    'smtp_port' => $_ENV['MAIL_SMTP_PORT'] ?? null,
                    'smtp_user' => $_ENV['MAIL_SMTP_USER'] ?? null,
                    'smtp_password' => $_ENV['MAIL_SMTP_PASSWORD'] ?? null,
                ],
            ]);
        }
    ]);
};