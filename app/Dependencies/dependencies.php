<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $paths = [
        __DIR__ . "/../../src/Infrastructure/Database/ManagerDatabase.php",
        __DIR__ . "/repositories.php",
        __DIR__ . "/settings.php",
        __DIR__ . "/MailConfig.php",
    ];

    foreach ($paths as $path) {
        (require $path)($containerBuilder);
    }
    $containerBuilder->addDefinitions([

        \App\Domain\Shared\Interfaces\MailerInterface::class => function (ContainerInterface $c) {
            $mailConfig = $c->get(\App\Infrastructure\Mail\MailConfig::class);

            $from = $mailConfig->from ?? 'no-reply@example.com';
            $smtpHost = $mailConfig->host !== '' ? $mailConfig->host : null;
            $port = $mailConfig->port ?? 587;
            $user = $mailConfig->user ?? null;
            $pass = $mailConfig->password ?? null;
            $secure = $mailConfig->secure ?? 'tls';

            return new \App\Infrastructure\Mail\PHPMailerMailer($smtpHost, $port, $user, $pass, $from, $secure);
        },

        \App\Infrastructure\Listeners\SendEmailListener::class => function (ContainerInterface $c) {
            return new \App\Infrastructure\Listeners\SendEmailListener(
                $c->get(\App\Domain\Shared\Interfaces\MailerInterface::class)
            );
        },

        \App\Infrastructure\Events\EventDispatcher::class => function (ContainerInterface $c) {
            $dispatcher = new \App\Infrastructure\Events\EventDispatcher();

            $emailListener = $c->get(\App\Infrastructure\Listeners\SendEmailListener::class);
            $dispatcher->listen(
                \App\Domain\Shared\Events\EmailEventInterface::class,
                fn($event) => $emailListener->handle($event)
            );

            return $dispatcher;
        },
    ]);
};