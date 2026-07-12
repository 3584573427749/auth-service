<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\UserInUseException;
use App\Domain\ValueObjects\UserId;

class DeleteUserHandler extends UserHandler {
    /**
     * SoftDelete, sätter deleted_at fältet i databasen till nuvarande tid.
     * och tar bort alla poster ur kopplade tabeller.
     * @throws NotFoundException
     */
    public function softDelete(UserId $id) : void {
        try {
            $this->db->beginTransaction();
            $this->repository->softDelete($id);

            $this->userRoleRepository->deleteByUser($id);
            $this->db->commit();
        } catch (NotFoundException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * HardDelete, tar bort posten ur databasen och alla kopplade tabeller.
     * @throws UserInUseException
     * @throws NotFoundException
     */
    public function removeUser(UserId $id) : void {
        try {
            $this->db->beginTransaction();
            $this->userRoleRepository->deleteByUser($id);

            $this->repository->remove($id);
            $this->db->commit();
        } catch (NotFoundException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
