<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\DataTransportObjects\User;

use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\Entities\Role;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\RoleId;
use PHPUnit\Framework\TestCase;

final class RoleDTOTest extends TestCase {
    public function testFromRoleCreatesDto() : void {
        $role = new Role(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
            'User',
            'Name',
            1,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $dto = RoleDTO::fromRole($role);

        $data = $dto->jsonSerialize();

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $data['id']);
        self::assertSame('User', $data['name']);
        self::assertSame('Name', $data['description']);
        self::assertSame(1, $data['adminLevel']);
    }

    public function testJsonSerializeReturnsCorrectStructure() : void {
        $role = new Role(
            new RoleId('660e8400-e29b-41d4-a716-446655440000'),
            'Another',
            'User',
            1,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $dto = RoleDTO::fromRole($role);

        $data = $dto->jsonSerialize();

        self::assertArrayHasKey('id', $data);
        self::assertArrayHasKey('name', $data);
        self::assertArrayHasKey('description', $data);
        self::assertArrayHasKey('adminLevel', $data);
    }
}
