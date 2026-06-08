<?php

namespace App\Application\Commands\User;

class CreateUserCommand {
    private function __construct(public string $email, public string $firstName, public string $lastName) {

    }

    public static function fromRequest(array $data): self {
        // Normalisera
        $email = $data['email']
                |> trim(...)
                |> strtolower(...);
        $firstName = trim($data['firstName']);
        $lastName = trim($data['email']);

        return new self($email, $firstName, $lastName);
    }
}