<?php

declare(strict_types=1);

use App\Domain\Repositories\RoleRepository;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\UserRoleRepository;
use App\Infrastructure\Database\DbalRoleRepository;
use App\Infrastructure\Database\DbalUserRepository;
use App\Infrastructure\Database\DbalUserRoleRepository;

use function DI\autowire;

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Repository-mappningar
    $containerBuilder->addDefinitions([
        UserRepository::class => autowire(DbalUserRepository::class),
        RoleRepository::class => autowire(DbalRoleRepository::class),
        UserRoleRepository::class => autowire(DbalUserRoleRepository::class),
    ]);
};
