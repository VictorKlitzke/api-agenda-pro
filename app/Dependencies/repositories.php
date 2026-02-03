<?php

declare(strict_types=1);

use App\Domain\User\Interfaces\UserInterface;
use App\Infrastructure\Persistence\User\DoctrineUserRepository;
use App\Domain\Company\Interfaces\CompanyInterface;
use App\Infrastructure\Persistence\Company\DoctrineCompanyRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Map the domain UserInterface to the Doctrine implementation
    $containerBuilder->addDefinitions([
        UserInterface::class => \DI\autowire(DoctrineUserRepository::class),
        CompanyInterface::class => \DI\autowire(DoctrineCompanyRepository::class),
        \App\Domain\Product\Interfaces\ProductInterface::class => \DI\autowire(\App\Infrastructure\Persistence\Product\DoctrineProductRepository::class),
        \App\Domain\Service\Interfaces\ServiceInterface::class => \DI\autowire(\App\Infrastructure\Persistence\Service\DoctrineServiceRepository::class),
    ]);
};
