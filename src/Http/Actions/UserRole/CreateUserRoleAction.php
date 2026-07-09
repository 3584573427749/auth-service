<?php

declare(strict_types=1);

namespace App\Http\Actions\UserRole;

use App\Application\Commands\UserRole\UserRoleCommand;
use App\Application\Handlers\UserRole\SaveUserRoleHandler;
use App\Domain\Exception\UserRoleAlreadyExistsException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class CreateUserRoleAction extends UserRoleAction {
    public function __construct(LoggerInterface $logger, private SaveUserRoleHandler $handler) {
        parent::__construct($logger);
    }

    /**
     * @throws UserRoleAlreadyExistsException
     */
    protected function action(): Response {
        //AnvändarId kommer från url
        $userId = $this->request->getAttribute('id');
var_dump($this->request->getAttributes());exit;
        // RollId kommer från body
        $data = (array)$this->request->getParsedBody();
        $data['userId'] = $userId;

       $userRoleCommand = UserRoleCommand::fromRequest($data);

        $this->handler->handle($userRoleCommand);

        return $this->respondWithData(null, 204);

    }
}
