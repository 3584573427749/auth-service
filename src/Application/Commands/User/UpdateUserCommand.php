<?php

declare(strict_types=1);

namespace App\Application\Commands\User;

use App\Domain\ValueObjects\UserId;

class UpdateUserCommand {
    /**
     * @param string[] $roles
     */
    private function __construct(
        public UserId $id,
        public string $email,
        public string $firstName,
        public string $lastName,
        public array $roles,
    ) {

    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromRequest(array $data) : self {
        // Normalisera
        $id = new UserId($data['id']);
        $email = $data['email']
                |> trim(...)
                |> strtolower(...);
        $firstName = trim($data['firstName']);
        $lastName = trim($data['lastName']);
        $roles = $data['roles'] ?? [];

        return new self($id, $email, $firstName, $lastName, $roles);
    }
}
