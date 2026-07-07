<?php

declare(strict_types=1);

namespace App\Application\Handlers\Roles;

use App\Domain\Exception\RoleInUseException;
use App\Domain\ValueObjects\RoleId;

/**
 * @throws RoleInUseException if the role is in use by any user
 */
class DeleteRoleHandler extends RoleHandler {
    public function handle(RoleId $id) : void {
        $this->repository->delete($id);
    }
}
