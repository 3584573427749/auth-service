<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;

class UserRole implements \JsonSerializable {
    public function __construct(
        private UserId $userId,
        private RoleId $roleId
    ) {
    }

    /**
     * @param array<string,mixed> $row
     */
    public static function fromDBRow(array $row): self {
        return new self(
            new UserId($row['user_id']),
            new RoleId($row['role_id'])
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function asDBRow(): array {
        return [
            'user_id' => $this->getUserId()->toString(),
            'role_id' => $this->getRoleId()->toString(),
        ];
    }

    public function getUserId(): UserId {
        return $this->userId;
    }

    public function getRoleId(): RoleId {
        return $this->roleId;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed {
        return [
            'user_id' => $this->getUserId()->toString(),
            'role_id' => $this->getRoleId()->toString(),
        ];
    }
}
