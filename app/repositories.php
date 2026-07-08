<?php

declare(strict_types=1);

use App\Domain\Repositories\RoleRepository;
use App\Domain\Repositories\UserRepository;
use App\Infrastructure\Database\DbalRoleRepository;
use App\Infrastructure\Database\DbalUserRepository;
use DI\ContainerBuilder;
use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    // Repository-mappningar
    $containerBuilder->addDefinitions([
        UserRepository::class => autowire(DbalUserRepository::class),
        RoleRepository::class => autowire(DbalRoleRepository::class),
    ]);
};
