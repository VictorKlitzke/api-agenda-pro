<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use App\Application\Settings\SettingsInterface;
use App\Infrastructure\Mail\MailConfig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        MailConfig::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class)->get('mail') ?? [];

            $enabled = isset($settings['enabled'])
                ? filter_var((string) $settings['enabled'], FILTER_VALIDATE_BOOLEAN)
                : true;
            $from = isset($settings['from']) ? (string)$settings['from'] : 'no-reply@example.com';
            $host = isset($settings['smtp_host']) ? (string)$settings['smtp_host'] : '';
            $port = isset($settings['smtp_port']) ? (int)$settings['smtp_port'] : 587;
            $user = $settings['smtp_user'] ?? null;
            $password = $settings['smtp_password'] ?? null;
            $secure = isset($settings['secure']) ? (string)$settings['secure'] : 'tls';

            return new MailConfig(
                enabled: $enabled,
                from: $from,
                host: $host,
                port: $port,
                user: $user,
                password: $password,
                secure: $secure,
            );
        }
    ]);
};
