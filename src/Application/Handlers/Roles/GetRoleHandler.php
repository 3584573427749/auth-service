<?php

declare(strict_types=1);

namespace App\Application\Handlers\Roles;

use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\ValueObjects\RoleId;

class GetRoleHandler extends RoleHandler {
    /**
     * @return RoleDTO[]
     */
    public function getAll(): array {
        $roles = $this->repository->getAll();

        // Lägg till läsning av roller

        $roleDTOs = [];
        foreach ($roles as $role) {
            $roleDTOs[] = RoleDTO::fromRole($role);
        }

        return $roleDTOs;
    }

    public function getById(RoleId $id): RoleDTO {
        $role = $this->repository->getById($id);

        return RoleDTO::fromRole($role);
    }
}
