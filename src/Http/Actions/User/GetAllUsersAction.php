<?php

declare(strict_types=1);

namespace App\Http\Actions\User;

use App\Application\Handlers\User\GetUserHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class GetAllUsersAction extends UserAction {
    public function __construct(LoggerInterface $logger, private GetUserHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @inheritDoc
     */
    protected function action() : Response {
        $users = $this->handler->getAll();

        return $this->respondWithData($users);
    }
}
