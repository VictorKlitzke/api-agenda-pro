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

        \App\Domain\Shared\Interfaces\WhatsappNotifierInterface::class => function () {
            $enabled = filter_var($_ENV['WHATSAPP_ENABLED'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
            $provider = strtolower(trim((string) ($_ENV['WHATSAPP_PROVIDER'] ?? 'unofficial_api')));
            $defaultCountryCode = trim((string) ($_ENV['WHATSAPP_DEFAULT_COUNTRY_CODE'] ?? '55'));

            if (!$enabled) {
                return new \App\Infrastructure\Notifications\WhatsApp\NullWhatsappNotifier();
            }

            if ($provider === 'infobip') {
                $baseUrl = trim((string) ($_ENV['WHATSAPP_INFOBIP_BASE_URL'] ?? ''));
                $apiKey = trim((string) ($_ENV['WHATSAPP_INFOBIP_API_KEY'] ?? ''));
                $sender = trim((string) ($_ENV['WHATSAPP_INFOBIP_SENDER'] ?? ''));
                $callbackData = trim((string) ($_ENV['WHATSAPP_INFOBIP_CALLBACK_DATA'] ?? ''));

                if ($baseUrl === '' || $apiKey === '' || $sender === '') {
                    return new \App\Infrastructure\Notifications\WhatsApp\NullWhatsappNotifier();
                }

                return new \App\Infrastructure\Notifications\WhatsApp\InfobipWhatsappNotifier(
                    $baseUrl,
                    $apiKey,
                    $sender,
                    $defaultCountryCode,
                    $callbackData
                );
            }

            $endpoint = trim((string) ($_ENV['WHATSAPP_UNOFFICIAL_ENDPOINT'] ?? ''));
            $token = trim((string) ($_ENV['WHATSAPP_UNOFFICIAL_TOKEN'] ?? ''));
            $tokenHeader = trim((string) ($_ENV['WHATSAPP_UNOFFICIAL_TOKEN_HEADER'] ?? 'Authorization'));
            $tokenPrefix = trim((string) ($_ENV['WHATSAPP_UNOFFICIAL_TOKEN_PREFIX'] ?? 'Bearer'));
            $phoneField = trim((string) ($_ENV['WHATSAPP_UNOFFICIAL_PHONE_FIELD'] ?? 'number'));
            $messageField = trim((string) ($_ENV['WHATSAPP_UNOFFICIAL_MESSAGE_FIELD'] ?? 'text'));
            $extraPayloadRaw = trim((string) ($_ENV['WHATSAPP_UNOFFICIAL_EXTRA_PAYLOAD_JSON'] ?? ''));

            $extraPayload = [];
            if ($extraPayloadRaw !== '') {
                $decoded = json_decode($extraPayloadRaw, true);
                if (is_array($decoded)) {
                    $extraPayload = $decoded;
                }
            }

            if ($provider !== 'unofficial_api' || $endpoint === '') {
                return new \App\Infrastructure\Notifications\WhatsApp\NullWhatsappNotifier();
            }

            return new \App\Infrastructure\Notifications\WhatsApp\UnofficialApiWhatsappNotifier(
                $endpoint,
                $token,
                $tokenHeader,
                $tokenPrefix,
                $phoneField,
                $messageField,
                $extraPayload,
                $defaultCountryCode
            );
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