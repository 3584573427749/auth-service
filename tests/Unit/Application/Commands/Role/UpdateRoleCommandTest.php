<?php

declare(strict_types=1);

namespace Application\Commands\Role;

use App\Application\Commands\Role\UpdateRoleCommand;
use PHPUnit\Framework\TestCase;

final class UpdateRoleCommandTest extends TestCase {
    public function testFromRequestMapsValuesCorrectly(): void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'Test Role',
            'description' => 'Test Role',
            'adminLevel' => 1,
            'createdAt' => '2026-01-01 10:00:00',
            'updatedAt' => '2026-01-01 10:00:01',
        ];;
        $command = UpdateRoleCommand::fromRequest($data);

        self::assertSame('Test role', $command->name);
        self::assertSame('Test Role', $command->description);
        self::assertSame(1, $command->adminLevel);
    }

    public function testNameIsTrimmedAndUppercasedFirst(): void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => '        test role        ',
            'description' => 'test role',
            'adminLevel' => 1,
            'createdAt' => '2026-01-01 10:00:00',
            'updatedAt' => '2026-01-01 10:00:01',
        ];

        $command = UpdateRoleCommand::fromRequest($data);

        self::assertSame('Test role', $command->name);
    }

    public function testDescriptionIsTrimmed(): void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'Test Role',
            'description' => '        test role        ',
            'adminLevel' => 1,
            'createdAt' => '2026-01-01 10:00:00',
            'updatedAt' => '2026-01-01 10:00:01',
        ];

        $command = UpdateRoleCommand::fromRequest($data);

        self::assertSame('test role', $command->description);
    }
}
