<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Application\Commands\User\UpdateUserCommand;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;

class UpdateUserHandler extends UserHandler {
    public function handle(UpdateUserCommand $command): UserDTO {
        $this->db->beginTransaction();
        try {
            if ($this->userRepository->emailExistsWithOtherUser($command->email, $command->id)) {
                throw new UserAlreadyExistsException('Användaren finns redan');
            }

            $user = $this->userRepository->getById($command->id);

            $user->setEmail(Email::fromString($command->email));
            $user->setfirstName($command->firstName);
            $user->setLastName($command->lastName);
            (bool)$command->isActive ? $user->activate() : $user->deactivate();
            $user->setCreatedAt($command->createdAt);
            $user->setUpdatedAt(new DateTimeValue('now'));

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
