<?php

declare(strict_types=1);

namespace App\Application\Handlers\Role;

use App\Domain\Repositories\RoleRepository;

abstract class RoleHandler {
    public function __construct(protected RoleRepository $repository) {

    }
}
