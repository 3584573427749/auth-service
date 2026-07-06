<?php

declare(strict_types=1);

namespace App\Domain\DataTransportObjects\Role;

use App\Domain\Entities\Role;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\RoleId;

readonly class RoleDTO implements \JsonSerializable {
    private function __construct(
        private RoleId $id,
        private string $name,
        private string $description,
        private int $adminLevel,
        private ?DateTimeValue $updatedAt,
        private DateTimeValue $createdAt,
    ) {
    }

    public static function fromRole(Role $role) : self {
        return new self(
            $role->getId(),
            $role->getName(),
            $role->getDescription(),
            $role->getAdminLevel(),
            $role->getUpdatedAt(),
            $role->getCreatedAt(),
        );
    }

    /**
     * @return array<string, string|int>
     */
    public function jsonSerialize() : array {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name,
            'description' => $this->description,
            'adminLevel' => $this->adminLevel,
            'updatedAt' => $this->updatedAt?->toISOString(),
            'createdAt' => $this->createdAt->toISOString(),
        ];
    }
}
