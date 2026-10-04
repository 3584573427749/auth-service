<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\OneTimeCodeId;
use App\Domain\ValueObjects\UserId;

class OneTimeCode implements \JsonSerializable {
    public function __construct(
        private OneTimeCodeId $id,
        private UserId $userId,
        private string $codeHash,
        private DateTimeValue $expiresAt,
        private ?DateTimeValue $consumedAt,
        private DateTimeValue $createdAt,
    ) {
    }

    /**
     * @param array<string,mixed> $row
     */
    public static function fromDBRow(array $row) : self {
        return new self(
            new OneTimeCodeId($row['id']),
            new UserId($row['user_id']),
            $row['code_hash'],
            new DateTimeValue($row['expires_at']),
            !empty($row['consumed_at'])
                ? new DateTimeValue($row['consumed_at'])
                : null,
            new DateTimeValue($row['created_at']),
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function asDBRow() : array {
        return [
            'id' => $this->id->toString(),
            'user_id' => $this->userId->toString(),
            'code_hash' => $this->codeHash,
            'expires_at' => $this->expiresAt->toString(),
            'consumed_at' => $this->consumedAt?->toString(),
            'created_at' => $this->createdAt->toString(),
        ];
    }

    public function getId() : OneTimeCodeId {
        return $this->id;
    }

    public function getUserId() : UserId {
        return $this->userId;
    }

    public function getCodeHash() : string {
        return $this->codeHash;
    }

    public function getExpiresAt() : DateTimeValue {
        return $this->expiresAt;
    }

    public function getConsumedAt() : ?DateTimeValue {
        return $this->consumedAt;
    }

    public function getCreatedAt() : DateTimeValue {
        return $this->createdAt;
    }

    public function consume() : void {
        $this->consumedAt = new DateTimeValue('now');
    }

    public function isConsumed() : bool {
        return $this->consumedAt !== null;
    }

    public function isExpired() : bool {
        return $this->expiresAt->isPast();
    }

    /**
     * @return string[]
     */
    public function jsonSerialize() : array {
        return [
            'id' => $this->id->toString(),
            'userId' => $this->userId->toString(),
            'expiresAt' => $this->expiresAt->toISOString(),
            'consumedAt' => $this->consumedAt?->toISOString(),
            'createdAt' => $this->createdAt->toISOString(),
        ];
    }
}
