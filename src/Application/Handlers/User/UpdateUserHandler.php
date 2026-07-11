<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Application\Commands\User\UpdateUserCommand;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\Role;
use App\Domain\Entities\UserRole;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\RoleId;

class UpdateUserHandler extends UserHandler {
    public function handle(UpdateUserCommand $command) : UserDTO {
        $this->db->beginTransaction();
        try {
            if ($this->repository->emailExistsWithOtherUser($command->email, $command->id)) {
                throw new UserAlreadyExistsException('Användaren finns redan');
            }

            $user = $this->repository->getById($command->id);

            $user->setEmail(Email::fromString($command->email));
            $user->setfirstName($command->firstName);
            $user->setLastName($command->lastName);
            $user->setUpdatedAt(new DateTimeValue('now'));

            $this->repository->save($user);

            $currentRoles = $this->userRoleRepository->getRoles($user->getId());
            $currentRoleIds = array_map(static fn (Role $role) => $role->getId()->toString(), $currentRoles);
            $rolesToAdd = array_diff(
                $command->roles,
                $currentRoleIds,
            );
            foreach ($rolesToAdd as $roleId) {
                $this->userRoleRepository->save(new UserRole($user->getId(), new RoleId($roleId)));
            }

            $rolesToRemove = array_diff(
                $currentRoleIds,
                $command->roles,
            );
            foreach ($rolesToRemove as $roleId) {
                $this->userRoleRepository->delete(new UserRole($user->getId(), new RoleId($roleId)));
            }

            $this->db->commit();

            $userDto = UserDTO::fromUser($user)->withRoles($command->roles);

            return $userDto;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

    }
}
