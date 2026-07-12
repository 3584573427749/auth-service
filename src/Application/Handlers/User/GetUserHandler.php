<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\Role;
use App\Domain\ValueObjects\UserId;

class GetUserHandler extends UserHandler {
    /**
     * @return UserDTO[]
     */
    public function getAll() : array {
        $users = $this->repository->getAll();

        // Lägg till läsning av roller

        $userDTOs = [];
        foreach ($users as $user) {
            $roles = $this->userRoleRepository->getRoles($user->getId());
            $roleIds = array_map(static fn (Role $role) => $role->getId()->toString(), $roles);
            $userDTOs[] = UserDTO::fromUser($user)->withRoles($roleIds);
        }

        return $userDTOs;
    }

    public function getById(UserId $id) : UserDTO {
        $user = $this->repository->getById($id);

        $roles = $this->userRoleRepository->getRoles($user->getId());
        $roleIds = array_map(static fn (Role $role) => $role->getId()->toString(), $roles);

        return UserDTO::fromUser($user)->withRoles($roleIds);
    }
}
