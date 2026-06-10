<?php

declare(strict_types=1);

namespace App\Domain\DataTransportObjects;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\UserId;

class CreateUserDTO implements \JsonSerializable
{
    /**
     * @param array <string> $roles
     */
    private function __construct(
        private readonly UserId $userId,
        private readonly string $email,
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly array $roles,
    ) {
    }

    public static function fromUser(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getEmail()->toString(),
            $user->getFirstName(),
            $user->getLastName(),
            ['user'],
        );
    }

    /**
     * @return array<string, string|array<string>>
     */
    public function jsonSerialize(): array
    {
        return [
            'userId' => $this->userId->toString(),
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'roles' => $this->roles,
        ];
    }
}
