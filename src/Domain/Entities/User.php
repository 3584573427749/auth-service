<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;

class User implements \JsonSerializable {
    public function __construct(
        private UserId $id,
        private Email $email,
        private string $firstName,
        private string $lastName,
        private DateTimeValue $createdAt,
        private ?DateTimeValue $updatedAt,
        private ?DateTimeValue $deletedAt,
    ) {
    }

    /**
     * @param array<string,mixed> $row
     */
    public static function fromDBRow(array $row) : self {
        return new self(
            new UserId($row['id']),
            new Email($row['email']),
            $row['first_name'],
            $row['last_name'],
            new DateTimeValue($row['created_at']),
            !empty($row['updated_at']) ? new DateTimeValue($row['updated_at']) : null,
            !empty($row['deleted_at']) ? new DateTimeValue($row['deleted_at']) : null,
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function asDBRow() : array {
        return [
            'id' => $this->getId()->toString(),
            'email' => $this->getEmail()->toString(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'created_at' => $this->getCreatedAt()->toString(),
            'updated_at' => $this->getUpdatedAt()?->toString(),
            'deleted_at' => $this->getDeletedAt()?->toString(),
        ];

    }

    public function getId() : UserId {
        return $this->id;
    }

    public function getEmail() : Email {
        return $this->email;
    }

    public function getFirstName() : string {
        return $this->firstName;
    }

    public function getLastName() : string {
        return $this->lastName;
    }

    public function getCreatedAt() : DateTimeValue {
        return $this->createdAt;
    }

    public function getUpdatedAt() : ?DateTimeValue {
        return $this->updatedAt;
    }

    public function getDeletedAt() : ?DateTimeValue {
        return $this->deletedAt;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() : mixed {
        return [
            'id' => $this->getId()->toString(),
            'email' => $this->getEmail()->toString(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'createdAt' => $this->getCreatedAt()->toString(),
            'updatedAt' => $this->getUpdatedAt()?->toString(),
            'deletedAt' => $this->getDeletedAt()?->toString(),
        ];
    }

    public function setEmail(Email $email) : void {
        $this->email = $email;
    }

    public function setFirstName(string $firstName) : void {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName) : void {
        $this->lastName = $lastName;
    }

    public function setUpdatedAt(DateTimeValue $updatedAt) : void {
        $this->updatedAt = $updatedAt;
    }
}
