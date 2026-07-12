<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Database;

use App\Domain\Entities\UserRole;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\UserRoleAlreadyExistsException;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Database\DbalUserRoleRepository;

final class DbalUserRoleRepositoryTest extends DatabaseBaseTestCase {
    private DbalUserRoleRepository $repository;

    protected function setUp() : void {
        parent::setUp();

        $this->loadSchema('roles');
        $this->loadSchema('users');
        $this->loadSchema('user_roles');

        $this->repository = new DbalUserRoleRepository($this->connection);
    }

    public function testSaveCreatesUserRole() : void {
        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'first_name' => 'Test',
                'last_name' => 'User',
                'created_at' => '2026-01-01 10:00:00',
            ],
        ]);

        $this->seed('roles', [
            [
                'id' => '660e8400-e29b-41d4-a716-446655440000',
                'name' => 'Admin',
                'description' => 'Administrator',
                'admin_level' => 100,
                'created_at' => '2026-01-01 10:00:00',
            ],
        ]);

        $this->repository->save(
            new UserRole(
                new UserId('550e8400-e29b-41d4-a716-446655440000'),
                new RoleId('660e8400-e29b-41d4-a716-446655440000'),
            ),
        );

        $count = $this->connection
            ->executeQuery('SELECT COUNT(*) FROM user_roles')
            ->fetchOne();

        self::assertSame(1, (int)$count);
    }

    public function testSaveThrowsExceptionWhenUserRoleAlreadyExists() : void {
        $this->seed('user_roles', [
            [
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'role_id' => '660e8400-e29b-41d4-a716-446655440000',
            ],
        ]);

        $this->expectException(
            UserRoleAlreadyExistsException::class,
        );

        $this->repository->save(
            new UserRole(
                new UserId('550e8400-e29b-41d4-a716-446655440000'),
                new RoleId('660e8400-e29b-41d4-a716-446655440000'),
            ),
        );
    }

    public function testDeleteRemovesUserRole() : void {
        $this->seed('user_roles', [
            [
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'role_id' => '660e8400-e29b-41d4-a716-446655440000',
            ],
        ]);

        $this->repository->delete(
            new UserRole(
                new UserId('550e8400-e29b-41d4-a716-446655440000'),
                new RoleId('660e8400-e29b-41d4-a716-446655440000'),
            ),
        );

        $count = $this->connection
            ->executeQuery('SELECT COUNT(*) FROM user_roles')
            ->fetchOne();

        self::assertSame(0, (int)$count);
    }

    public function testDeleteThrowsNotFoundException() : void {
        $this->expectException(NotFoundException::class);

        $this->repository->delete(
            new UserRole(
                new UserId('550e8400-e29b-41d4-a716-446655440000'),
                new RoleId('660e8400-e29b-41d4-a716-446655440000'),
            ),
        );
    }

    public function testGetRolesReturnsRolesForUser() : void {
        $this->seed('roles', [
            [
                'id' => '660e8400-e29b-41d4-a716-446655440000',
                'name' => 'Admin',
                'description' => 'Administrator',
                'admin_level' => 100,
                'created_at' => '2026-01-01 10:00:00',
            ],
        ]);

        $this->seed('user_roles', [
            [
                'user_id' => '550e8400-e29b-41d4-a716-446655440001',
                'role_id' => '660e8400-e29b-41d4-a716-446655440000',
            ],
        ]);

        $roles = $this->repository->getRoles(
            new UserId('550e8400-e29b-41d4-a716-446655440001'),
        );

        self::assertCount(1, $roles);
    }

    public function testGetRolesReturnsEmptyArray() : void {
        $roles = $this->repository->getRoles(
            new UserId('550e8400-e29b-41d4-a716-446655440001'),
        );

        self::assertSame([], $roles);
    }

    public function testGetUsersReturnsUsersForRole() : void {
        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'first_name' => 'Test',
                'last_name' => 'User',
                'created_at' => '2026-01-01 10:00:00',
            ],
        ]);

        $this->seed('user_roles', [
            [
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'role_id' => '660e8400-e29b-41d4-a716-446655440001',
            ],
        ]);

        $users = $this->repository->getUsers(
            new RoleId('660e8400-e29b-41d4-a716-446655440001'),
        );

        self::assertCount(1, $users);
    }

    public function testGetUsersReturnsEmptyArray() : void {
        $users = $this->repository->getUsers(
            new RoleId('660e8400-e29b-41d4-a716-446655440001'),
        );

        self::assertSame([], $users);
    }

    public function testDeleteByUserRemovesUserRoles() : void {
        $this->seed('user_roles', [
            [
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'role_id' => '660e8400-e29b-41d4-a716-446655440000',
            ],
            [
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'role_id' => '660e8400-e29b-41d4-a716-446655440001',
            ],
            [
                'user_id' => '550e8400-e29b-41d4-a716-446655440001',
                'role_id' => '660e8400-e29b-41d4-a716-446655440001',
            ],
        ]);

        $this->repository->deleteByUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
        );

        $count = $this->connection
            ->executeQuery('SELECT COUNT(*) FROM user_roles')
            ->fetchOne();

        self::assertSame(1, (int)$count);

    }
}
