<?php

declare(strict_types=1);

namespace App\Application\Handlers\UserRole;

use App\Application\Commands\UserRole\UserRoleCommand;
use App\Domain\Entities\UserRole;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;

class SaveUserRoleHandler extends UserRoleHandler {
    public function handle(UserRoleCommand $command) : void {
        $userId = new UserId($command->userId);
        $roleId = new RoleId($command->roleId);

        // Check if user and role exists
        $this->userRepository->getById($userId);
        $this->roleRepository->getById($roleId);
        $userRole = new UserRole(
            $userId,
            $roleId,
        );

        $this->repository->save($userRole);
    }
}
