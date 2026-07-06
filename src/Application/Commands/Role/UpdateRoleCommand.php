<?php

declare(strict_types=1);

namespace App\Application\Commands\Role;

use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\RoleId;

class UpdateRoleCommand {
    private function __construct(
        public RoleId $id,
        public string $name,
        public string $description,
        public int $adminLevel,
        public DateTimeValue $createdAt,
        public ?DateTimeValue $updatedAt,
    ) {

    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromRequest(array $data): self {
        // Normalisera
        $id = new RoleId($data['id']);
        $name = $data['name']
                |> trim(...)
                |> mb_strtolower(...)
                |> mb_ucfirst(...);
        $description = trim($data['description']);
        $adminLevel = (int)$data['adminLevel'];
        $createdAt = new DateTimeValue($data['createdAt']);
        $updatedAt = isset($data['updatedAt']) ? new DateTimeValue($data['updatedAt']) : null;

        return new self($id, $name, $description, $adminLevel, $createdAt, $updatedAt);
    }
}
