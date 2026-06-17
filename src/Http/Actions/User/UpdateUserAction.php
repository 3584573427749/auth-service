<?php

declare(strict_types=1);

namespace App\Http\Actions\User;

use App\Application\Commands\User\UpdateUserCommand;
use App\Application\Handlers\User\UpdateUserHandler;
use App\Application\Validators\UpdateUserRequestValidator;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class UpdateUserAction extends UserAction {
    public function __construct(LoggerInterface $logger, private UpdateUserHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @throws UserAlreadyExistsException
     */
    protected function action() : Response {
        $data = (array)$this->request->getParsedBody();
        $data['userId'] = $this->request->getAttribute('id');

        //Validera API-data
        $errors = UpdateUserRequestValidator::validate($data);
        if (count($errors) > 0) {
            throw new ValidationException('Felaktig indata', $errors);
        }

        $userCommand = UpdateUserCommand::fromRequest($data);

        $dto = $this->handler->handle($userCommand);

        return $this->respondWithData($dto, 201);

    }
}
