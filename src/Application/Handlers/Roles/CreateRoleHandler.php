<?php

declare(strict_types=1);

namespace App\Application\Handlers\Roles;

use App\Application\Commands\Role\CreateRoleCommand;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\Entities\Role;
use App\Domain\Exception\RoleAlreadyExistsException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\RoleId;

class CreateRoleHandler extends RoleHandler {
    public function handle(CreateRoleCommand $command): RoleDTO {
        $this->db->beginTransaction();
        try {
            $role = new Role(
                new RoleId(),
                $command->name,
                $command->description,
                $command->adminLevel,
                new DateTimeValue('now'),
                null,
            );

            $this->repository->save($role);

            $this->db->commit();

        } catch (RoleAlreadyExistsException $e) {
            $this->db->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        $roleDto = RoleDTO::fromRole($role);

        return $roleDto;

    }
}
