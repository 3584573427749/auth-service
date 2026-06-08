<?php

namespace App\Domain\Entities;


use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;

class User implements \JsonSerializable {
    public function __construct(private UserId $id, private Email $email, private string $firstName, private string $lastName, private bool $isActive,
        private DateTimeValue $createdAt, private ?DateTimeValue $updatedAt) {
    }

    public static function fromDBRow(array $row): self {
        return new self(
            new UserId($row['id']),
            new Email($row['email']),
            $row['first_name'],
            $row['last_name'],
            (bool)$row['is_active'],
            new DateTimeValue($row['created_at']),
            !empty($row['updated_at']) ? new DateTimeValue($row['updated_at']) : null
        );
    }

    public function asDBRow(): array {
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
    public function jsonSerialize(): mixed {
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

    /**
     * @return UserId
     */
    public function getId(): UserId {
        return $this->id;
    }


    /**
     * @return Email
     */
    public function getEmail(): Email {
        return $this->email;
    }

    /**
     * @param Email $email
     */
    public function setEmail(Email $email): void {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstName(): string {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }

    /**
     * @return bool
     */
    public function isActive(): bool {
        return $this->isActive;
    }

    /**
     * @return void
     */
    public function activate(): void {
        $this->isActive = true;
    }

    /**
     * @return void
     */
    public function deactivate(): void {
        $this->isActive = false;
    }

    /**
     * @return DateTimeValue
     */
    public function getCreatedAt(): DateTimeValue {
        return $this->createdAt;
    }

    /**
     * @return DateTimeValue
     */
    public function getUpdatedAt(): ?DateTimeValue {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeValue $updatedAt
     */
    public function setUpdatedAt(DateTimeValue $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }


}