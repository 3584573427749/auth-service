<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Application\Commands\User\CreateUserCommand;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;

class CreateUserHandler extends UserHandler {
    public function handle(CreateUserCommand $command) : UserDTO {
        $this->db->beginTransaction();
        try {
            if ($this->userRepository->existsByEmail($command->email)) {
                throw new UserAlreadyExistsException('Användaren finns redan');
            }

            $user = new User(
                new UserId(),
                Email::fromString($command->email),
                $command->firstName,
                $command->lastName,
                true,
                new DateTimeValue('now'),
                null,
            );

            $this->userRepository->save($user);

            $this->db->commit();

            $userDto = UserDTO::fromUser($user);

            return $userDto;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

    }
}
