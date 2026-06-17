<?php

declare(strict_types=1);

namespace App\Application\Commands\User;

use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;

class UpdateUserCommand {
    private function __construct(
        public UserId $id,
        public string $email,
        public string $firstName,
        public string $lastName,
        public int $isActive,
        public DateTimeValue $createdAt,
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
        $isActive = $data['isActive'] ? 1 : 0;
        $createdAt = new DateTimeValue($data['createdAt']);

        return new self($id, $email, $firstName, $lastName, $isActive, $createdAt);
    }
}
