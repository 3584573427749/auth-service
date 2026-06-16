<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\DataTransportObjects\User;

use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class UserDTOTest extends TestCase {
    public function testFromUserCreatesDto() : void {
        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('test@example.com'),
            'User',
            'Name',
            true,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $dto = UserDTO::fromUser($user);

        $data = $dto->jsonSerialize();

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $data['id']);
        self::assertSame('test@example.com', $data['email']);
        self::assertSame('User', $data['firstName']);
        self::assertSame('Name', $data['lastName']);
        self::assertSame(['user'], $data['roles']);
    }

    public function testJsonSerializeReturnsCorrectStructure() : void {
        $user = new User(
            new UserId('660e8400-e29b-41d4-a716-446655440000'),
            new Email('another@example.com'),
            'Another',
            'User',
            true,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $dto = UserDTO::fromUser($user);

        $data = $dto->jsonSerialize();

        self::assertArrayHasKey('id', $data);
        self::assertArrayHasKey('email', $data);
        self::assertArrayHasKey('firstName', $data);
        self::assertArrayHasKey('lastName', $data);
        self::assertArrayHasKey('roles', $data);
    }

    public function testRolesAlwaysContainsDefaultUserRole() : void {
        $user = new User(
            new UserId(),
            new Email('role@test.com'),
            'Role',
            'User',
            true,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $dto = UserDTO::fromUser($user);

        $data = $dto->jsonSerialize();

        self::assertSame(['user'], $data['roles']);
    }
}
