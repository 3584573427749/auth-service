<?php

declare(strict_types=1);

namespace App\Application\Handlers\UserRole;

use App\Domain\Repositories\RoleRepository;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\UserRoleRepository;

abstract class UserRoleHandler {
    public function __construct(
        protected UserRoleRepository $repository,
        protected UserRepository $userRepository,
        protected RoleRepository $roleRepository,
    ) {

    }
}
