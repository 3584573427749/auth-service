<?php

declare(strict_types=1);

namespace App\Domain\DataTransportObjects\User;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;

readonly class UserDTO implements \JsonSerializable {
    /**
     * @param array<string> $roles
     */
    private function __construct(
        private UserId $id,
        private string $email,
        private string $firstName,
        private string $lastName,
        private ?DateTimeValue $updatedAt,
        private DateTimeValue $createdAt,
        private ?DateTimeValue $deletedAt,
        private array $roles,
    ) {
    }

    public static function fromUser(User $user) : self {
        return new self(
            $user->getId(),
            $user->getEmail()->toString(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getUpdatedAt() ?? null,
            $user->getCreatedAt(),
            $user->getDeletedAt() ?? null,
            [],
        );
    }

    /**
     * @param string[] $roles
     */
    public function withRoles(array $roles) : self {
        return new self(
            $this->id,
            $this->email,
            $this->firstName,
            $this->lastName,
            $this->updatedAt,
            $this->createdAt,
            $this->deletedAt,
            $roles,
        );
    }

    /**
     * @return array<string, string|array<string>>
     */
    public function jsonSerialize() : array {
        return [
            'id' => $this->id->toString(),
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'roles' => $this->roles,
            'updatedAt' => $this->updatedAt?->toISOString(),
            'createdAt' => $this->createdAt->toISOString(),
            'deletedAt' => $this->deletedAt?->toISOString(),
        ];
    }
}
