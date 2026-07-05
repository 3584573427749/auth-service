<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\RoleId;

class Role implements \JsonSerializable {
    public function __construct(
        private RoleId $id,
        private string $name,
        private string $description,
        private int $admin_level,
        private DateTimeValue $createdAt,
        private ?DateTimeValue $updatedAt,
    ) {
    }

    /**
     * @param array<string,mixed> $row
     */
    public static function fromDBRow(array $row) : self {
        return new self(
            new RoleId($row['id']),
            $row['name'],
            $row['description'],
            (int)$row['admin_level'],
            new DateTimeValue($row['created_at']),
            !empty($row['updated_at']) ? new DateTimeValue($row['updated_at']) : null,
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function asDBRow() : array {
        return [
            'id' => $this->getId()->toString(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'admin_level' => $this->getAdminLevel(),
            'created_at' => $this->getCreatedAt()->toString(),
            'updated_at' => $this->getUpdatedAt()?->toString(),
        ];

    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() : mixed {
        return [
            'id' => $this->getId()->toString(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'adminLevel' => $this->getAdminLevel(),
            'createdAt' => $this->getCreatedAt()->toString(),
            'updatedAt' => $this->getUpdatedAt()?->toString(),
        ];
    }

    public function getId() : RoleId {
        return $this->id;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getDescription() : string {
        return $this->description;
    }

    public function getAdminLevel() : int {
        return $this->admin_level;
    }

    public function getCreatedAt() : DateTimeValue {
        return $this->createdAt;
    }

    public function setName(string $name) : void {
        $this->name = $name;
    }

    public function setDescription(string $description) : void {
        $this->description = $description;
    }

    public function setAdminLevel(int $admin_level) : void {
        $this->admin_level = $admin_level;
    }

    public function getUpdatedAt() : ?DateTimeValue {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeValue $updatedAt) : void {
        $this->updatedAt = $updatedAt;
    }
}
