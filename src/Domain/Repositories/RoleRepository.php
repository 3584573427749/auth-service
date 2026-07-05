<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Role;
use App\Domain\ValueObjects\RoleId;

interface RoleRepository {
    public function save(Role $role) : void;

    /**
     * @return Role[]
     */
    public function getAll() : array;

    public function getById(RoleId $id) : Role;

    public function delete(RoleId $id) : void;
}
