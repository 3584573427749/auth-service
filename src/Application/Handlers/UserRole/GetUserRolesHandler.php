<?php

declare(strict_types=1);

namespace App\Application\Handlers\UserRole;

use App\Application\Handlers\User\UserHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;

class GetUserRolesHandler extends UserRoleHandler {
    /**
     * @return RoleDTO[]
     */
    public function getRoles(UserId $id) : array {
        $roles = $this->repository->getRoles($id);

        $roleDTOs = [];
        foreach ($roles as $role) {
            $roleDTOs[] = RoleDTO::fromRole($role);
        }

        return $roleDTOs;
    }
    public function getUsers(RoleId $id) : array {
        $users = $this->repository->getUsers($id);

        $userDTOs = [];
        foreach ($users as $user) {
            $userDTOs[] = UserDTO::fromUser($user);
        }

        return $userDTOs;
    }

}
