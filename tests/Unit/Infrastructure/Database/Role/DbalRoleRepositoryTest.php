<?php

declare(strict_types=1);

namespace Infrastructure\Database\Role;

use App\Domain\Entities\Role;
use App\Domain\Exception\NotFoundException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\RoleId;
use App\Infrastructure\Database\Role\DbalRoleRepository;
use Tests\Unit\Infrastructure\Database\DatabaseBaseTestCase;

final class DbalRoleRepositoryTest extends DatabaseBaseTestCase {
    private DbalRoleRepository $repository;

    public function testSaveInsertsNewUser(): void {
        $role = $this->createRole();

        $this->repository->save($role);

        $row = $this->connection->fetchAssociative(
            'SELECT * FROM roles WHERE id = :id',
            ['id' => $role->getId()->toString()],
        );

        self::assertNotFalse($row);
        self::assertSame('User', $row['name']);
    }

    private function createRole(?DateTimeValue $updatedAt = null): Role {
        return new Role(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
            'User',
            'Name',
            1,
            new DateTimeValue('2026-01-01 10:00:00'),
            $updatedAt,
        );
    }

    public function testSaveUpdatesExistingRole(): void {
        $role = $this->createRole();

        // First insert
        $this->connection->insert('roles', $role->asDBRow());

        // Modify role
        $role->setName('Updated');
        $role->setUpdatedAt(new DateTimeValue('2026-01-02 10:00:00'));

        $this->repository->save($role);

        $row = $this->connection->fetchAssociative(
            'SELECT * FROM roles WHERE id = :id',
            ['id' => $role->getId()->toString()],
        );

        self::assertNotFalse($row);
        self::assertSame('Updated', $row['name']);
    }

    public function testGetAllReturnsEmptyArrayWhenNoRoles(): void {
        $this->loadSchema('roles');

        $repository = new DbalRoleRepository($this->connection);

        $result = $repository->getAll();

        self::assertSame([], $result);
    }

    public function testGetAllReturnsRoles(): void {
        $this->seed('roles', [
            [
                'id' => '660e8400-e29b-41d4-a716-446655440001',
                'name' => 'Admin',
                'description' => 'Administrator'
                , 'admin_level' => 1,
            ],
            [
                'id' => '660e8400-e29b-41d4-a716-446655440000',
                'name' => 'User',
                'description' => 'Regular user'
                , 'admin_level' => 0,
            ],
        ]);

        $repository = new DbalRoleRepository($this->connection);

        $result = $repository->getAll();

        self::assertCount(2, $result);

        self::assertSame('Admin', $result[0]->getName());
        self::assertSame('User', $result[1]->getName());
    }

    public function testGetByIdReturnsRole(): void {
        $this->loadSchema('roles');

        $this->seed('roles', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'User',
                'description' => 'Regular user',
                'admin_level' => 0,
            ],
        ]);

        $repository = new DbalRoleRepository($this->connection);

        $result = $repository->getById(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertInstanceOf(Role::class, $result);

        self::assertSame('User', $result->getName());
    }

    public function testGetByIdThrowsNotFoundException(): void {
        $this->loadSchema('roles');

        $repository = new DbalRoleRepository($this->connection);

        $this->expectException(NotFoundException::class);

        $repository->getById(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        );
    }

    public function testDelete(): void {
        $this->loadSchema('roles');

        $this->seed('roles', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'User',
                'description' => 'Regular user',
                'admin_level' => 0,
            ],
        ]);

        $repository = new DbalRoleRepository($this->connection);

        $repository->delete(new RoleId('550e8400-e29b-41d4-a716-446655440000'));
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM roles WHERE id = :id',
            ['id' => '550e8400-e29b-41d4-a716-446655440000'],
        );
        self::assertFalse($row);
    }

    protected function setUp(): void {
        parent::setUp();

        $this->loadSchema('roles');

        $this->repository = new DbalRoleRepository($this->connection);
    }
}
