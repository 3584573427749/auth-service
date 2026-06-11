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
        private bool $isActive,
        private DateTimeValue $createdAt,
        private ?DateTimeValue $updatedAt,
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
            (bool)$row['is_active'],
            new DateTimeValue($row['created_at']),
            !empty($row['updated_at']) ? new DateTimeValue($row['updated_at']) : null,
        );
    }

    /**
     * @return array<string|mixed>
     */
    public function asDBRow() : array {
        return [
            'id' => $this->getId()->toString(),
            'email' => $this->getEmail()->toString(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'is_active' => $this->isActive() ? 1 : 0,
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
            'email' => $this->getEmail()->toString(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'isActive' => $this->isActive(),
            'createdAt' => $this->getCreatedAt()->toString(),
            'updatedAt' => $this->getUpdatedAt()?->toString(),
        ];
    }

    public function getId() : UserId {
        return $this->id;
    }

    public function getEmail() : Email {
        return $this->email;
    }

    public function setEmail(Email $email) : void {
        $this->email = $email;
    }

    public function getFirstName() : string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName) : void {
        $this->firstName = $firstName;
    }

    public function getLastName() : string {
        return $this->lastName;
    }

    public function setLastName(string $lastName) : void {
        $this->lastName = $lastName;
    }

    public function isActive() : bool {
        return $this->isActive;
    }

    public function activate() : void {
        $this->isActive = true;
    }

    public function deactivate() : void {
        $this->isActive = false;
    }

    public function getCreatedAt() : DateTimeValue {
        return $this->createdAt;
    }

    public function getUpdatedAt() : ?DateTimeValue {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeValue $updatedAt) : void {
        $this->updatedAt = $updatedAt;
    }
}
