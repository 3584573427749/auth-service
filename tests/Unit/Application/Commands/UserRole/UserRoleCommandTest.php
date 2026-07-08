<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Commands\UserRole;

use App\Application\Commands\UserRole\UserRoleCommand;
use PHPUnit\Framework\TestCase;

final class UserRoleCommandTest extends TestCase {
    public function testFromRequestCreatesCommand() : void {
        $command = UserRoleCommand::fromRequest([
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'roleId' => '660e8400-e29b-41d4-a716-446655440000',
        ]);

        self::assertInstanceOf(
            UserRoleCommand::class,
            $command,
        );
    }

    public function testFromRequestMapsValuesCorrectly() : void {
        $command = UserRoleCommand::fromRequest([
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'roleId' => '660e8400-e29b-41d4-a716-446655440000',
        ]);

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $command->userId,
        );

        self::assertSame(
            '660e8400-e29b-41d4-a716-446655440000',
            $command->roleId,
        );
    }
}
