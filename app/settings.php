<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true,
                'logError' => false,
                'logErrorDetails' => false,       
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