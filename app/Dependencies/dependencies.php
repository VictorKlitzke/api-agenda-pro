<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

return function (ContainerBuilder $containerBuilder) {

    $paths = [
        __DIR__ . "/../../src/Infrastructure/Database/ManagerDatabase.php",
        __DIR__ . "/repositories.php",
        __DIR__ . "/../settings.php",
        __DIR__ . "/mailConfig.php",
    ];

    foreach ($paths as $path) {
        (require $path)($containerBuilder);
    }
    $containerBuilder->addDefinitions([

        LoggerInterface::class => function () {
            $logPath = __DIR__ . '/../../var/logs/app.log';
            $logDir = dirname($logPath);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            $logger = new Logger('app');
            $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
            return $logger;
        },

        \App\Domain\Shared\Interfaces\MailerInterface::class => function (ContainerInterface $c) {
            $mailConfig = $c->get(\App\Infrastructure\Mail\MailConfig::class);

            if (!$mailConfig->enabled || trim($mailConfig->host) === '') {
                return new \App\Infrastructure\Mail\NullMailer();
            }

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
        
        \App\Application\Middleware\PlanLimitMiddleware::class => function (ContainerInterface $c) {
            return new \App\Application\Middleware\PlanLimitMiddleware(
                $c->get(\App\Domain\Company\Repositories\CompanyRepository::class),
                $c->get(\App\Domain\CompanyPlan\Repositories\CompanyPlanRepository::class),
                $c->get(\App\Domain\Agendamentos\Repositories\AgendamentoRepository::class)
            );
        },

        \App\Application\Middleware\ProfessionalLimitMiddleware::class => function (ContainerInterface $c) {
            return new \App\Application\Middleware\ProfessionalLimitMiddleware(
                $c->get(\App\Domain\Company\Repositories\CompanyRepository::class),
                $c->get(\App\Domain\CompanyPlan\Repositories\CompanyPlanRepository::class),
                $c->get(\App\Domain\Profissionals\Repositories\ProfissionalRepository::class)
            );
        },
    ]);
};