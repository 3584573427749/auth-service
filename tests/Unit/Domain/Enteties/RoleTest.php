<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\Role;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\RoleId;
use PHPUnit\Framework\TestCase;

final class RoleTest extends TestCase {
    private RoleId $id;

    private string $name;

    private string $description;

    private int $adminLevel;

    private DateTimeValue $createdAt;

    protected function setUp() : void {
        $this->id = new RoleId('550e8400-e29b-41d4-a716-446655440000');
        $this->name = 'User';
        $this->description = 'Regular user';
        $this->adminLevel = 1;
        $this->createdAt = new DateTimeValue('2026-06-10 10:00:00');
    }

    public function testConstructorAndGetters() : void {
        $role = new Role(
            $this->id,
            $this->name,
            $this->description,
            $this->adminLevel,
            $this->createdAt,
            null,
        );

        self::assertSame($this->id, $role->getId());
        self::assertSame($this->name, $role->getName());
        self::assertSame($this->description, $role->getDescription());
        self::assertSame($this->adminLevel, $role->getAdminLevel());
    }

    public function testSetters() : void {
        $role = new Role(
            $this->id,
            $this->name,
            $this->description,
            $this->adminLevel,
            $this->createdAt,
            null,
        );

        $newName = 'NewUser';
        $role->setName($newName);

        $newDescription = 'New user description';
        $role->setDescription($newDescription);

        $newAdminLevel = 2;
        $role->setAdminLevel($newAdminLevel);

        self::assertSame($newName, $role->getName());
        self::assertSame($newDescription, $role->getDescription());
        self::assertSame($newAdminLevel, $role->getAdminLevel());
    }

    public function testSetUpdatedAt() : void {
        $role = new Role(
            $this->id,
            $this->name,
            $this->description,
            $this->adminLevel,
            $this->createdAt,
            null,
        );

        $updatedAt = new DateTimeValue('2026-06-11 10:00:00');
        $role->setUpdatedAt($updatedAt);

        self::assertSame($updatedAt, $role->getUpdatedAt());
    }

    public function testFromDBRow() : void {
        $row = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'User',
            'description' => 'Regular user',
            'admin_level' => 1,
            'created_at' => '2026-06-10 10:00:00',
            'updated_at' => null,
        ];

        $role = Role::fromDBRow($row);

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $role->getId()->toString());
        self::assertSame('User', $role->getName());
        self::assertSame('Regular user', $role->getDescription());
        self::assertSame(1, $role->getAdminLevel());
        self::assertNull($role->getUpdatedAt());
    }

    public function testAsDBRow() : void {
        $role = new Role(
            $this->id,
            $this->name,
            $this->description,
            $this->adminLevel,
            $this->createdAt,
            null,
        );

        $row = $role->asDBRow();

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $row['id']);
        self::assertSame('User', $row['name']);
        self::assertSame('Regular user', $row['description']);
        self::assertSame(1, $row['admin_level']);
        self::assertSame('2026-06-10 10:00:00', $row['created_at']);
        self::assertNull($row['updated_at']);
    }

    public function testJsonSerialize() : void {
        $role = new Role(
            $this->id,
            $this->name,
            $this->description,
            $this->adminLevel,
            $this->createdAt,
            null,
        );

        $data = $role->jsonSerialize();

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $data['id']);
        self::assertSame('User', $data['name']);
        self::assertSame('Regular user', $data['description']);
        self::assertSame(1, $data['adminLevel']);
        self::assertSame('2026-06-10 10:00:00', $data['createdAt']);
        self::assertNull($data['updatedAt']);
    }
}
