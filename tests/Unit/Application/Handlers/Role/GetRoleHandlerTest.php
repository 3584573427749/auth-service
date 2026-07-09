<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\Role;

use App\Application\Handlers\Role\GetRoleHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\Entities\Role;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\RoleRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\RoleId;
use PHPUnit\Framework\TestCase;

final class GetRoleHandlerTest extends TestCase {
    public function testGetAllReturnsRoleDTOs() : void {
        $role1 = new Role(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
            'A',
            'User',
            1,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $role2 = new Role(
            new RoleId('660e8400-e29b-41d4-a716-446655440000'),
            'B',
            'User',
            1,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $repository = $this->createMock(RoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$role1, $role2]);

        $handler = new class($repository) extends GetRoleHandler {
            public function __construct(RoleRepository $repository) {
                $this->repository = $repository;
            }
        };

        $result = $handler->getAll();

        self::assertCount(2, $result);

        self::assertInstanceOf(RoleDTO::class, $result[0]);
        self::assertInstanceOf(RoleDTO::class, $result[1]);
    }

    public function testGetAllReturnsEmptyArrayWhenNoRoles() : void {
        $repository = $this->createMock(RoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $handler = new class($repository) extends GetRoleHandler {
            public function __construct(RoleRepository $repository) {
                $this->repository = $repository;
            }
        };

        $result = $handler->getAll();

        self::assertSame([], $result);
    }

    public function testGetByIdReturnsRoleDTO() : void {
        $role = new Role(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
            'A',
            'User',
            1,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $repository = $this->createMock(RoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($role);

        $handler = new class($repository) extends GetRoleHandler {
            public function __construct(RoleRepository $repository) {
                $this->repository = $repository;
            }
        };

        $result = $handler->getById(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertInstanceOf(RoleDTO::class, $result);
    }

    public function testGetByIdThrowsRoleNotFoundException() : void {
        $repository = $this->createMock(RoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('getById')
            ->willThrowException(new NotFoundException('Role not found'));

        $handler = new class($repository) extends GetRoleHandler {
            public function __construct(RoleRepository $repo) {
                $this->repository = $repo;
            }
        };

        $this->expectException(NotFoundException::class);

        $handler->getById(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        );
    }
}
