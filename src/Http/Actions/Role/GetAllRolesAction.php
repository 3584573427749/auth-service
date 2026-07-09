<?php

declare(strict_types=1);

namespace App\Http\Actions\Role;

use App\Application\Handlers\Role\GetRoleHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class GetAllRolesAction extends RoleAction {
    public function __construct(LoggerInterface $logger, private GetRoleHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @inheritDoc
     */
    protected function action() : Response {
        $RoleDTOs = $this->handler->getAll();

        return $this->respondWithData($RoleDTOs);
    }
}
