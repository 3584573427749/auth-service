<?php

declare(strict_types=1);

namespace App\Http\Actions\UserRole;

use App\Application\Handlers\UserRole\GetUserRolesHandler;
use App\Domain\ValueObjects\UserId;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class GetUserRolesAction extends UserRoleAction {
    public function __construct(LoggerInterface $logger, private GetUserRolesHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @inheritDoc
     */
    protected function action() : Response {
        $id = $this->request->getAttribute('id');
        $userId = new UserId($id);
        $rolesDTO = $this->handler->getRoles($userId);

        return $this->respondWithData($rolesDTO);
    }
}
