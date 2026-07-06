<?php

declare(strict_types=1);

namespace App\Http\Actions\Roles;

use App\Application\Commands\Role\UpdateRoleCommand;
use App\Application\Handlers\Roles\UpdateRoleHandler;
use App\Application\Validators\UpdateRoleRequestValidator;
use App\Domain\Exception\RoleAlreadyExistsException;
use App\Domain\Exception\ValidationException;
use App\Http\Actions\User\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class UpdateRoleAction extends RoleAction {
    public function __construct(LoggerInterface $logger, private UpdateRoleHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @throws RoleAlreadyExistsException
     */
    protected function action(): Response {
        $data = (array)$this->request->getParsedBody();
        $data['roleId'] = $this->request->getAttribute('id');

        //Validera API-data
        $errors = UpdateRoleRequestValidator::validate($data);
        if (count($errors) > 0) {
            throw new ValidationException('Felaktig indata', $errors);
        }

        $roleCommand = UpdateRoleCommand::fromRequest($data);

        $dto = $this->handler->handle($roleCommand);

        return $this->respondWithData($dto, 200);

    }
}
