<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase {
    private UserId $id;

    private Email $email;

    private DateTimeValue $createdAt;

    protected function setUp() : void {
        $this->id = new UserId('550e8400-e29b-41d4-a716-446655440000');
        $this->email = new Email('test@example.com');
        $this->createdAt = new DateTimeValue('2026-06-10 10:00:00');
    }

    public function testConstructorAndGetters() : void {
        $user = new User(
            $this->id,
            $this->email,
            'User',
            'Name',
            true,
            $this->createdAt,
            null,
        );

        self::assertSame($this->id, $user->getId());
        self::assertSame($this->email, $user->getEmail());
        self::assertSame('User', $user->getFirstName());
        self::assertSame('Name', $user->getLastName());
        self::assertTrue($user->isActive());
        self::assertSame($this->createdAt, $user->getCreatedAt());
        self::assertNull($user->getUpdatedAt());
    }

    public function testSetters() : void {
        $user = new User(
            $this->id,
            $this->email,
            'User',
            'Name',
            true,
            $this->createdAt,
            null,
        );

        $newEmail = new Email('new@example.com');
        $user->setEmail($newEmail);

        $user->setFirstName('NewFirst');
        $user->setLastName('NewLast');

        self::assertSame($newEmail, $user->getEmail());
        self::assertSame('NewFirst', $user->getFirstName());
        self::assertSame('NewLast', $user->getLastName());
    }

    public function testActivateDeactivate() : void {
        $user = new User(
            $this->id,
            $this->email,
            'User',
            'Name',
            false,
            $this->createdAt,
            null,
        );

        self::assertFalse($user->isActive());

        $user->activate();
        self::assertTrue($user->isActive());

        $user->deactivate();
        self::assertFalse($user->isActive());
    }

    public function testSetUpdatedAt() : void {
        $user = new User(
            $this->id,
            $this->email,
            'User',
            'Name',
            true,
            $this->createdAt,
            null,
        );

        $updatedAt = new DateTimeValue('2026-06-11 10:00:00');
        $user->setUpdatedAt($updatedAt);

        self::assertSame($updatedAt, $user->getUpdatedAt());
    }

    public function testFromDBRow() : void {
        $row = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'first_name' => 'User',
            'last_name' => 'Name',
            'is_active' => 1,
            'created_at' => '2026-06-10 10:00:00',
            'updated_at' => null,
        ];

        $user = User::fromDBRow($row);

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $user->getId()->toString());
        self::assertSame('test@example.com', $user->getEmail()->toString());
        self::assertSame('User', $user->getFirstName());
        self::assertSame('Name', $user->getLastName());
        self::assertTrue($user->isActive());
    }

    public function testAsDBRow() : void {
        $user = new User(
            $this->id,
            $this->email,
            'User',
            'Name',
            true,
            $this->createdAt,
            null,
        );

        $row = $user->asDBRow();

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $row['id']);
        self::assertSame('test@example.com', $row['email']);
        self::assertSame('User', $row['first_name']);
        self::assertSame('Name', $row['last_name']);
        self::assertSame(1, $row['is_active']);
        self::assertSame('2026-06-10 10:00:00', $row['created_at']);
        self::assertNull($row['updated_at']);
    }

    public function testJsonSerialize() : void {
        $user = new User(
            $this->id,
            $this->email,
            'User',
            'Name',
            true,
            $this->createdAt,
            null,
        );

        $data = $user->jsonSerialize();

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $data['id']);
        self::assertSame('test@example.com', $data['email']);
        self::assertSame('User', $data['firstName']);
        self::assertSame('Name', $data['lastName']);
        self::assertTrue($data['isActive']);
        self::assertSame('2026-06-10 10:00:00', $data['createdAt']);
        self::assertNull($data['updatedAt']);
    }
}
