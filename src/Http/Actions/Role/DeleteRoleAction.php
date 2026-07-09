<?php

declare(strict_types=1);

namespace App\Http\Actions\Role;

use App\Application\Handlers\Role\DeleteRoleHandler;
use App\Domain\ValueObjects\RoleId;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class DeleteRoleAction extends RoleAction {
    public function __construct(LoggerInterface $logger, private DeleteRoleHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @inheritDoc
     */
    protected function action() : Response {
        $id = $this->request->getAttribute('id');
        $roleId = new RoleId($id);
        $this->handler->handle($roleId);

        return $this->response->withStatus(204);
    }
}
