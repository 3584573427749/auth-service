<?php

declare(strict_types=1);

namespace App\Http\Actions\Role;

use App\Application\Handlers\Role\GetRoleHandler;
use App\Domain\ValueObjects\RoleId;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class GetRoleAction extends RoleAction {
    public function __construct(LoggerInterface $logger, private GetRoleHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @inheritDoc
     */
    protected function action() : Response {
        $id = $this->request->getAttribute('id');
        $roleId = new RoleId($id);
        $roleDTO = $this->handler->getById($roleId);

        return $this->respondWithData($roleDTO);
    }
}
