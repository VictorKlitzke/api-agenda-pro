<?php

declare(strict_types=1);

use App\Domain\User\Repositories\UserRepository;
use App\Domain\Auth\Repositories\LoginAttemptRepository;
use App\Domain\Auth\Repositories\UserTokenRepository;
use DI\ContainerBuilder;
use Domain\Company\Repositories\CompanyRepository;
use function DI\autowire;
use function DI\create;
use function DI\get;


return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        ClientRepository::class => autowire()
            ->constructorParameter('connection', get('db.agendapro')),
        UserRepository::class => autowire()
            ->constructorParameter('connection', get('db.agendapro')),
        CompanyRepository::class => autowire()
            ->constructorParameter('connection', get('db.agendapro')),

        LoginAttemptRepository::class => autowire()
            ->constructorParameter('connection', get('db.agendapro')),

        UserTokenRepository::class => autowire()
            ->constructorParameter('connection', get('db.agendapro')),

    ]);
};
