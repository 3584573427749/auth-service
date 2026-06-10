<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\MagicLinkId;
use App\Domain\ValueObjects\UserId;

class MagicLinks implements \JsonSerializable
{
    public function __construct(
        private MagicLinkId $id,
        private UserId $userId,
        private string $token_hash,
        private string $clientType,
        private DateTimeValue $expiresAt,
        private DateTimeValue $consumedAt,
        private DateTimeValue $createdAt,
    ) {
    }

    /**
     * @return array <string, string>
     */
    public function asDBRow(): array
    {
        return [
            'id' => $this->id->toString(),
            'user_id' => $this->userId->toString(),
            'token_hash' => $this->token_hash,
            'client_type' => $this->clientType,
            'expires_at' => $this->expiresAt->toString(),
            'consumed_at' => $this->consumedAt->toString(),
            'created_at' => $this->createdAt->toString(),
        ];
    }

    /**
     * @param array<string, mixed> $row DB row with keys matching the database columns
     */
    public static function fromDBRow(array $row): self
    {
        return new self(
            new MagicLinkId($row['id']),
            new UserId($row['user_id']),
            $row['token_hash'],
            $row['client_type'],
            new DateTimeValue($row['expires_at']),
            new DateTimeValue($row['consumed_at']),
            new DateTimeValue($row['created_at']),
        );
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'userId' => $this->userId->toString(),
            'tokenHash' => $this->token_hash,
            'clientType' => $this->clientType,
            'expiresAt' => $this->expiresAt->toString(),
            'consumedAt' => $this->consumedAt->toString(),
            'createdAt' => $this->createdAt->toString(),
        ];
    }
}
