<?php

declare(strict_types=1);

namespace App\Http\Actions\UserRole;

use App\Application\Commands\UserRole\UserRoleCommand;
use App\Application\Handlers\UserRole\DeleteUserRoleHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class DeleteUserRoleAction extends UserRoleAction {
    public function __construct(LoggerInterface $logger, private DeleteUserRoleHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @inheritDoc
     */
    protected function action(): Response {
        $id = $this->request->getAttribute('id');
        $roleId = $this->request->getAttribute('roleId');
        $command = UserRoleCommand::fromRequest(['userId' => $id, 'roleId' => $roleId]);

        $this->handler->handle($command);

        return $this->respondWithData(null, 204);
    }
}
