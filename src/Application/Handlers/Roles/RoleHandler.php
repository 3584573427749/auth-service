<?php

declare(strict_types=1);

namespace App\Application\Handlers\Roles;

use App\Domain\Repositories\RoleRepository;
use Doctrine\DBAL\Connection;

abstract class RoleHandler {
    public function __construct(protected Connection $db, protected RoleRepository $roleRepository) {

    }
}
