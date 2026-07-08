<?php

declare(strict_types=1);

namespace App\Http\Actions\User;

use App\Application\Handlers\User\GetUserHandler;
use App\Domain\ValueObjects\UserId;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class GetUserAction extends UserAction {
    public function __construct(LoggerInterface $logger, private GetUserHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @inheritDoc
     */
    protected function action() : Response {
        $id = $this->request->getAttribute('id');
        $userId = new UserId($id);
        $userDTO = $this->handler->getById($userId);

        return $this->respondWithData($userDTO);
    }
}
