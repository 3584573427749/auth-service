<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Database\User;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Database\User\DbalUserRepository;
use Tests\Unit\Infrastructure\Database\DatabaseBaseTestCase;

final class DbalUserRepositoryTest extends DatabaseBaseTestCase {
    private DbalUserRepository $repository;

    protected function setUp() : void {
        parent::setUp();

        $this->loadSchema('users');

        $this->repository = new DbalUserRepository($this->connection);
    }

    private function createUser(?DateTimeValue $updatedAt = null) : User {
        return new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('test@example.com'),
            'User',
            'Name',
            true,
            new DateTimeValue('2026-01-01 10:00:00'),
            $updatedAt,
        );
    }

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
}
