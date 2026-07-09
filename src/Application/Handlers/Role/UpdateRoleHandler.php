<?php

declare(strict_types=1);

namespace App\Application\Handlers\Role;

use App\Application\Commands\Role\UpdateRoleCommand;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\Exception\RoleAlreadyExistsException;
use App\Domain\ValueObjects\DateTimeValue;

class UpdateRoleHandler extends RoleHandler {
    public function handle(UpdateRoleCommand $command) : RoleDTO {
        $this->db->beginTransaction();
        try {
            $role = $this->repository->getById($command->id);

            $role->setName($command->name);
            $role->setDescription($command->description);
            $role->setAdminLevel($command->adminLevel);
            $role->setCreatedAt($command->createdAt);
            $role->setUpdatedAt(new DateTimeValue('now'));

            $this->repository->save($role);

            $this->db->commit();

            $roleDto = RoleDTO::fromRole($role);

            return $roleDto;
        } catch (RoleAlreadyExistsException $e) {
            $this->db->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

    }
}
