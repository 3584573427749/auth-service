<?php

declare(strict_types=1);

namespace App\Http\Actions\User;

use App\Application\Commands\User\CreateUserCommand;
use App\Application\Handlers\User\CreateUserHandler;
use App\Application\Validators\CreateUserRequestValidator;
use App\Domain\Exception\UserAlreadyExistsException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class CreateUserAction extends UserAction {
    public function __construct(LoggerInterface $logger, private CreateUserHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @throws UserAlreadyExistsException
     */
    protected function action() : Response {
        $data = (array)$this->request->getParsedBody();

        //Validera API-data
        $errors = CreateUserRequestValidator::validate($data);
        if (count($errors) > 0) {
            return $this->respondWithData([
                'error' => 'Validation failed.',
                'fields' => $errors,
                'indata' => $data,
            ], 422);
        }

        $userCommand = CreateUserCommand::fromRequest($data);

        $dto = $this->handler->handle($userCommand);

        return $this->respondWithData($dto, 201);

    }
}
