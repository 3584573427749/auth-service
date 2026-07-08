<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Database\User;

use App\Domain\Entities\User;
use App\Domain\Exception\NotFoundException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Database\DbalUserRepository;
use Tests\Unit\Infrastructure\Database\DatabaseBaseTestCase;

final class DbalUserRepositoryTest extends DatabaseBaseTestCase {
    private DbalUserRepository $repository;

    public function testExistsByEmailReturnsFalseWhenUserDoesNotExist() : void {
        $exists = $this->repository->existsByEmail('test@example.com');

        self::assertFalse($exists);
    }

    public function testExistsByEmailReturnsTrueWhenUserExists() : void {
        $user = $this->createUser();

        // Insert manually
        $this->connection->insert('users', $user->asDBRow());

        $exists = $this->repository->existsByEmail('test@example.com');

        self::assertTrue($exists);
    }

    private function createUser(?DateTimeValue $updatedAt = null) : User {
        return new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('test@example.com'),
            'User',
            'Name',
            new DateTimeValue('2026-01-01 10:00:00'),
            $updatedAt,
            null,
        );
    }

    public function testSaveInsertsNewUser() : void {
        $user = $this->createUser();

        $this->repository->save($user);

        $row = $this->connection->fetchAssociative(
            'SELECT * FROM users WHERE id = :id',
            ['id' => $user->getId()->toString()],
        );

        self::assertNotFalse($row);
        self::assertSame('test@example.com', $row['email']);
    }

    public function testSaveUpdatesExistingUser() : void {
        $user = $this->createUser();

        // First insert
        $this->connection->insert('users', $user->asDBRow());

        // Modify user
        $user->setFirstName('Updated');
        $user->setUpdatedAt(new DateTimeValue('2026-01-02 10:00:00'));

        $this->repository->save($user);

        $row = $this->connection->fetchAssociative(
            'SELECT * FROM users WHERE id = :id',
            ['id' => $user->getId()->toString()],
        );

        self::assertNotFalse($row);
        self::assertSame('Updated', $row['first_name']);
    }

    public function testGetAllReturnsEmptyArrayWhenNoUsers() : void {
        $this->loadSchema('users');

        $repository = new DbalUserRepository($this->connection);

        $result = $repository->getAll();

        self::assertSame([], $result);
    }

    public function testGetAllReturnsUsers() : void {
        $this->seed('users', [
            [
                'id' => '660e8400-e29b-41d4-a716-446655440001',
                'email' => 'a@test.com',
                'first_name' => 'A',
                'last_name' => 'User',
                'created_at' => '2026-01-01 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => '660e8400-e29b-41d4-a716-446655440000',
                'email' => 'b@test.com',
                'first_name' => 'B',
                'last_name' => 'User',
                'created_at' => '2026-01-01 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);

        $repository = new DbalUserRepository($this->connection);

        $result = $repository->getAll();

        self::assertCount(2, $result);

        self::assertSame('a@test.com', $result[0]->getEmail()->toString());
        self::assertSame('b@test.com', $result[1]->getEmail()->toString());
    }

    public function testGetByIdReturnsUser() : void {
        $this->loadSchema('users');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'first_name' => 'User',
                'last_name' => 'Name',
                'created_at' => '2026-01-01 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);

        $repository = new DbalUserRepository($this->connection);

        $result = $repository->getById(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertInstanceOf(User::class, $result);

        self::assertSame(
            'test@example.com',
            $result->getEmail()->toString(),
        );

        self::assertSame('User', $result->getFirstName());
        self::assertSame('Name', $result->getLastName());
    }

    public function testGetByIdThrowsNotFoundException() : void {
        $this->loadSchema('users');

        $repository = new DbalUserRepository($this->connection);

        $this->expectException(NotFoundException::class);

        $repository->getById(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
        );
    }

    public function testDelete() : void {
        $this->loadSchema('users');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'first_name' => 'User',
                'last_name' => 'Name',
                'created_at' => '2026-01-01 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);

        $repository = new DbalUserRepository($this->connection);

        $repository->softDelete(new UserId('550e8400-e29b-41d4-a716-446655440000'));
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM users WHERE id = :id',
            ['id' => '550e8400-e29b-41d4-a716-446655440000'],
        );
        self::assertNotFalse($row);
        self::assertNotNull($row['deleted_at']);
    }

    public function testRemove() : void {
        $this->loadSchema('users');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'first_name' => 'User',
                'last_name' => 'Name',
                'created_at' => '2026-01-01 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);

        $repository = new DbalUserRepository($this->connection);

        $repository->remove(new UserId('550e8400-e29b-41d4-a716-446655440000'));
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM users WHERE id = :id',
            ['id' => '550e8400-e29b-41d4-a716-446655440000'],
        );
        self::assertFalse($row);
    }

    protected function setUp() : void {
        parent::setUp();

        $this->loadSchema('users');

        $this->repository = new DbalUserRepository($this->connection);
    }
}
