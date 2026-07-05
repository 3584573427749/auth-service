<?php

declare(strict_types=1);

namespace App\Http\Actions\Roles;

use App\Application\Commands\Role\CreateRoleCommand;
use App\Application\Handlers\Roles\CreateRoleHandler;
use App\Application\Validators\CreateRoleRequestValidator;
use App\Domain\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class CreateRoleAction extends RoleAction {
    public function __construct(LoggerInterface $logger, private CreateRoleHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $data = (array)$this->request->getParsedBody();

        //Validera API-data
        $errors = CreateRoleRequestValidator::validate($data);
        if (count($errors) > 0) {
            throw new ValidationException('Felaktig indata', $errors);
        }

        $roleCommand = CreateRoleCommand::fromRequest($data);

        $dto = $this->handler->handle($roleCommand);

        return $this->respondWithData($dto, 201);

    }
}
