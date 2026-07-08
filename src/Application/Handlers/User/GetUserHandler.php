<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\ValueObjects\UserId;

class GetUserHandler extends UserHandler {
    /**
     * @return UserDTO[]
     */
    public function getAll() : array {
        $users = $this->userRepository->getAll();

        // Lägg till läsning av roller

        $userDTOs = [];
        foreach ($users as $user) {
            $userDTOs[] = UserDTO::fromUser($user);
        }

        return $userDTOs;
    }

    public function getById(UserId $id) : UserDTO {
        $user = $this->userRepository->getById($id);

        return UserDTO::fromUser($user);
    }
}
