<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\UserRole;

use App\Application\Handlers\UserRole\GetUserRolesHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use App\Domain\Repositories\RoleRepository;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\UserRoleRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class GetUserRoleHandlerTest extends TestCase {
    public function testGetRolesReturnsRoleDTOs() : void {
        $role = new Role(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
            'Admin',
            'Administrator role',
            100,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $repository = $this->createMock(UserRoleRepository::class);
        $repository
            ->expects($this->once())
            ->method('getRoles')
            ->with(
                $this->equalTo(
                    new UserId('660e8400-e29b-41d4-a716-446655440000'),
                ),
            )
            ->willReturn([$role]);

        $userRepository = $this->createMock(UserRepository::class);
        $roleRepository = $this->createMock(RoleRepository::class);

        $handler = new GetUserRolesHandler(
            $this->createMock(Connection::class),
            $repository,
            $userRepository,
            $roleRepository,
        );

        $result = $handler->getRoles(
            new UserId('660e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertCount(1, $result);
        self::assertInstanceOf(RoleDTO::class, $result[0]);
    }

    public function testGetRolesReturnsEmptyArrayWhenNoRolesExist() : void {
        $repository = $this->createMock(UserRoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn([]);

        $userRepository = $this->createMock(UserRepository::class);
        $roleRepository = $this->createMock(RoleRepository::class);

        $handler = new GetUserRolesHandler(
            $this->createMock(Connection::class),
            $repository,
            $userRepository,
            $roleRepository,
        );

        $result = $handler->getRoles(
            new UserId('660e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertSame([], $result);
    }

    public function testGetUsersReturnsUserDTOs() : void {
        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('test@example.com'),
            'Test',
            'User',
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
            null,
        );

        $repository = $this->createMock(UserRoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('getUsers')
            ->with(
                $this->equalTo(
                    new RoleId('550e8400-e29b-41d4-a716-446655440000'),
                ),
            )
            ->willReturn([$user]);

        $userRepository = $this->createMock(UserRepository::class);
        $roleRepository = $this->createMock(RoleRepository::class);

        $handler = new GetUserRolesHandler(
            $this->createMock(Connection::class),
            $repository,
            $userRepository,
            $roleRepository,
        );

        $result = $handler->getUsers(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertCount(1, $result);
        self::assertInstanceOf(UserDTO::class, $result[0]);
    }

    public function testGetUsersReturnsEmptyArrayWhenNoUsersExist() : void {
        $repository = $this->createMock(UserRoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('getUsers')
            ->willReturn([]);

        $userRepository = $this->createMock(UserRepository::class);
        $roleRepository = $this->createMock(RoleRepository::class);

        $handler = new GetUserRolesHandler(
            $this->createMock(Connection::class),
            $repository,
            $userRepository,
            $roleRepository,
        );

        $result = $handler->getUsers(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertSame([], $result);
    }
}
