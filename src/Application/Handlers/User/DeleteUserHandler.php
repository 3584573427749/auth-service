<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Application\Handlers\UserRole\DeleteUserRoleHandler;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\UserInUseException;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Connection;

class DeleteUserHandler extends UserHandler {
    public function __construct(
        Connection $db,
        UserRepository $repository,
        private DeleteUserRoleHandler $deleteUserRoleHandler,
    ) {
        parent::__construct($db, $repository);
    }

    /**
     * SoftDelete, sätter deleted_at fältet i databasen till nuvarande tid,
     * tar bort alla poster ur kopplade tabeller,
     * och avaktiverar användaren om den finns i group-service.
     * @throws NotFoundException
     */
    public function softDelete(UserId $id) : void {
        try {
            $this->db->beginTransaction();

            $this->deleteUserRoleHandler->deleteAll($id);

            $this->repository->softDelete($id);
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

            $this->deleteUserRoleHandler->deleteAll($id);

            $this->repository->remove($id);
            $this->db->commit();
        } catch (NotFoundException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
