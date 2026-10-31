<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\OneTimeCode;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\OneTimeCodeId;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class OneTimeCodeTest extends TestCase {
    public function testFromDBRow() : void {
        $entity = OneTimeCode::fromDBRow([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'user_id' => '660e8400-e29b-41d4-a716-446655440000',
            'code_hash' => 'hash',
            'expires_at' => '2030-01-01 00:00:00',
            'consumed_at' => null,
            'created_at' => '2026-01-01 00:00:00',
        ]);

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $entity->getId()->toString(),
        );

        self::assertSame(
            '660e8400-e29b-41d4-a716-446655440000',
            $entity->getUserId()->toString(),
        );

        self::assertSame(
            'hash',
            $entity->getCodeHash(),
        );
    }

    public function testAsDBRow() : void {
        $entity = $this->createEntity();

        $row = $entity->asDBRow();

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $row['id'],
        );

        self::assertSame(
            '660e8400-e29b-41d4-a716-446655440000',
            $row['user_id'],
        );

        self::assertSame(
            'hash',
            $row['code_hash'],
        );
    }

    public function testGetters() : void {
        $entity = $this->createEntity();

        self::assertInstanceOf(
            OneTimeCodeId::class,
            $entity->getId(),
        );

        self::assertInstanceOf(
            UserId::class,
            $entity->getUserId(),
        );

        self::assertSame(
            'hash',
            $entity->getCodeHash(),
        );

        self::assertInstanceOf(
            DateTimeValue::class,
            $entity->getExpiresAt(),
        );

        self::assertInstanceOf(
            DateTimeValue::class,
            $entity->getCreatedAt(),
        );
    }

    public function testConsumeMarksCodeAsConsumed() : void {
        $entity = $this->createEntity();

        self::assertFalse(
            $entity->isConsumed(),
        );

        $entity->consume();

        self::assertTrue(
            $entity->isConsumed(),
        );

        self::assertNotNull(
            $entity->getConsumedAt(),
        );
    }

    public function testIsConsumedReturnsFalse() : void {
        self::assertFalse(
            $this->createEntity()->isConsumed(),
        );
    }

    public function testIsConsumedReturnsTrue() : void {
        $entity = new OneTimeCode(
            new OneTimeCodeId(
                '550e8400-e29b-41d4-a716-446655440000',
            ),
            new UserId(
                '660e8400-e29b-41d4-a716-446655440000',
            ),
            'hash',
            new DateTimeValue('+1 day'),
            new DateTimeValue('now'),
            new DateTimeValue('2026-01-01 00:00:00'),
        );

        self::assertTrue(
            $entity->isConsumed(),
        );
    }

    public function testIsExpiredReturnsFalse() : void {
        $entity = new OneTimeCode(
            new OneTimeCodeId(
                '550e8400-e29b-41d4-a716-446655440000',
            ),
            new UserId(
                '660e8400-e29b-41d4-a716-446655440000',
            ),
            'hash',
            new DateTimeValue('+1 day'),
            null,
            new DateTimeValue('2026-01-01 00:00:00'),
        );

        self::assertFalse(
            $entity->isExpired(),
        );
    }

    public function testIsExpiredReturnsTrue() : void {
        $entity = new OneTimeCode(
            new OneTimeCodeId(
                '550e8400-e29b-41d4-a716-446655440000',
            ),
            new UserId(
                '660e8400-e29b-41d4-a716-446655440000',
            ),
            'hash',
            new DateTimeValue('-1 day'),
            null,
            new DateTimeValue('2026-01-01 00:00:00'),
        );

        self::assertTrue(
            $entity->isExpired(),
        );
    }

    public function testJsonSerialize() : void {
        $entity = $this->createEntity();

        $json = $entity->jsonSerialize();

        self::assertArrayHasKey(
            'id',
            $json,
        );

        self::assertArrayHasKey(
            'userId',
            $json,
        );

        self::assertArrayHasKey(
            'expiresAt',
            $json,
        );

        self::assertArrayHasKey(
            'createdAt',
            $json,
        );
    }

    private function createEntity() : OneTimeCode {
        return new OneTimeCode(
            new OneTimeCodeId(
                '550e8400-e29b-41d4-a716-446655440000',
            ),
            new UserId(
                '660e8400-e29b-41d4-a716-446655440000',
            ),
            'hash',
            new DateTimeValue('+1 day'),
            null,
            new DateTimeValue('2026-01-01 00:00:00'),
        );
    }
}
