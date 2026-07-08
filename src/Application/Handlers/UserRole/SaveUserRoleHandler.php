<?php

declare(strict_types=1);

namespace App\Application\Handlers\UserRole;

use App\Application\Commands\UserRole\UserRoleCommand;
use App\Domain\Entities\UserRole;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;

class SaveUserRoleHandler extends UserRoleHandler {
    public function handle(UserRoleCommand $command) : void {
        $userRole = new UserRole(
            new UserId($command->userId),
            new RoleId($command->roleId),
        );

        $this->repository->save($userRole);
    }
}
