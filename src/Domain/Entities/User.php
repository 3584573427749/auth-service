<?php

namespace App\Domain\Entities;

use App\Domain\ValueObject\DateTimeValue;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObjects\UserId;

class User implements \JsonSerializable {
    private function __construct(UserId $id, Email $email, string $firstName, string $lastName, bool $isActive,
        DateTimeValue $createdAt, DateTimeValue $updatedAt) {}

    public static function create(array $data) {}

    /**
     * @inheritDoc
     */
    public function jsonSerialize():mixed {
        // TODO: Implement jsonSerialize() method.
    }
}