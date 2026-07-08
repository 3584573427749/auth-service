<?php

declare(strict_types=1);

namespace App\Application\Handlers\UserRole;

use App\Application\Commands\User\CreateUserCommand;
use App\Application\Commands\UserRole\UserRoleCommand;
use App\Application\Handlers\User\UserHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\Entities\UserRole;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
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
