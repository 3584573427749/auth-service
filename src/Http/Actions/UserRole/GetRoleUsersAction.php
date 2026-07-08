<?php

declare(strict_types=1);

namespace App\Http\Actions\UserRole;

use App\Application\Handlers\UserRole\GetUserRolesHandler;
use App\Domain\ValueObjects\RoleId;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class GetRoleUsersAction extends UserRoleAction {
    public function __construct(LoggerInterface $logger, private GetUserRolesHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @inheritDoc
     */
    protected function action() : Response {
        $id = $this->request->getAttribute('id');
        $roleId = new RoleId($id);
        $usersDTO = $this->handler->getUsers($roleId);

        return $this->respondWithData($usersDTO);
    }
}
