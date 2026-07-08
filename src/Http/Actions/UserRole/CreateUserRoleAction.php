<?php

declare(strict_types=1);

namespace App\Http\Actions\UserRole;

use App\Application\Commands\User\CreateUserCommand;
use App\Application\Commands\UserRole\UserRoleCommand;
use App\Application\Handlers\User\CreateUserHandler;
use App\Application\Handlers\UserRole\SaveUserRoleHandler;
use App\Application\Validators\CreateUserRequestValidator;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Exception\UserRoleAlreadyExistsException;
use App\Domain\Exception\ValidationException;
use App\Http\Actions\User\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class CreateUserRoleAction extends UserRoleAction {
    public function __construct(LoggerInterface $logger, private SaveUserRoleHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @throws UserRoleAlreadyExistsException
     */
    protected function action() : Response {
        $data = (array)$this->request->getParsedBody();

        $userRoleCommand = UserRoleCommand::fromRequest($data);

        $this->handler->handle($userRoleCommand);

        return $this->respondWithData(null, 204);

    }
}
