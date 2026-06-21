<?php

declare(strict_types=1);

namespace App\Http\Actions\User;

use App\Application\Handlers\User\DeleteUserHandler;
use App\Domain\ValueObjects\UserId;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class DeleteUserAction extends UserAction {
    public function __construct(LoggerInterface $logger, private DeleteUserHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @inheritDoc
     */
    protected function action(): Response {
        $id = $this->request->getAttribute('id');
        $userId = new UserId($id);
        $this->handler->handle($userId);

        return $this->response->withStatus(204);
    }
}
