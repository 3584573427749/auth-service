<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use App\Domain\Entities\UserRole;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;

interface UserRoleRepository {
    /**
     * @return User[]
     */
    public function getUsers(RoleId $id) : array;
    /**
     * @return Role[]
     */
    public function getRoles(UserId $id) : array;

    public function delete(UserRole $userRole) : void;

    public function save(UserRole $userRole) : void;
}
