<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Application\Commands\User\CreateUserCommand;
use App\Application\Handlers\UserRole\SaveUserRoleHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Connection;

class CreateUserHandler extends UserHandler {
    public function __construct(Connection $db, UserRepository $repository, private SaveUserRoleHandler $saveUserRoleHandler) {
        parent::__construct($db, $repository);
    }

    public function handle(CreateUserCommand $command) : UserDTO {
        $this->db->beginTransaction();
        try {
            if ($this->repository->existsByEmail($command->email)) {
                throw new UserAlreadyExistsException('Användaren finns redan');
            }

            $user = new User(
                new UserId(),
                Email::fromString($command->email),
                $command->firstName,
                $command->lastName,
                new DateTimeValue('now'),
                null,
                null,
            );

            $this->repository->save($user);

            $this->saveUserRoleHandler->saveAll($user->getId(), $command->roles);

            $this->db->commit();

        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
        $userDto = UserDTO::fromUser($user);

        return $userDto;

    }
}
