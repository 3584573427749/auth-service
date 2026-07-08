<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\UserRole;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class UserRoleTest extends TestCase {
    public function testConstructorAndGetters() : void {
        $userRole = new UserRole(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new RoleId('660e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $userRole->getUserId()->toString(),
        );

        self::assertSame(
            '660e8400-e29b-41d4-a716-446655440000',
            $userRole->getRoleId()->toString(),
        );
    }

    public function testFromDbRowCreatesEntity() : void {
        $userRole = UserRole::fromDBRow([
            'user_id' => '550e8400-e29b-41d4-a716-446655440000',
            'role_id' => '660e8400-e29b-41d4-a716-446655440000',
        ]);

        self::assertInstanceOf(UserRole::class, $userRole);

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $userRole->getUserId()->toString(),
        );

        self::assertSame(
            '660e8400-e29b-41d4-a716-446655440000',
            $userRole->getRoleId()->toString(),
        );
    }

    public function testAsDbRowReturnsExpectedArray() : void {
        $userRole = new UserRole(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new RoleId('660e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertSame(
            [
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'role_id' => '660e8400-e29b-41d4-a716-446655440000',
            ],
            $userRole->asDBRow(),
        );
    }

    public function testJsonSerializeReturnsExpectedArray() : void {
        $userRole = new UserRole(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new RoleId('660e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertSame(
            [
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'role_id' => '660e8400-e29b-41d4-a716-446655440000',
            ],
            $userRole->jsonSerialize(),
        );
    }
}
