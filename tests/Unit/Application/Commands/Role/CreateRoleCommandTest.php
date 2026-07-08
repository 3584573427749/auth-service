<?php

declare(strict_types=1);

namespace Application\Commands\Role;

use App\Application\Commands\Role\CreateRoleCommand;
use PHPUnit\Framework\TestCase;

final class CreateRoleCommandTest extends TestCase {
    public function testFromRequestMapsValuesCorrectly() : void {
        $data = [
            'name' => 'Test',
            'description' => 'Test role',
            'adminLevel' => 1,
        ];

        $command = CreateRoleCommand::fromRequest($data);

        self::assertSame('Test', $command->name);
        self::assertSame('Test role', $command->description);
        self::assertSame(1, $command->adminLevel);
    }

    public function testNameIsTrimmedAndFirstCharacherUpper() : void {
        $data = [
            'name' => '  TEST  ',
            'description' => 'Test user role',
            'adminLevel' => 1,
        ];

        $command = CreateRoleCommand::fromRequest($data);

        self::assertSame('Test', $command->name);
    }

    public function testNamesAreTrimmed() : void {
        $data = [
            'name' => 'Test',
            'description' => '  Test user role ',
            'adminLevel' => 1,
        ];

        $command = CreateRoleCommand::fromRequest($data);

        self::assertSame('Test user role', $command->description);
    }

    public function testAdminLevelIsCastToInt() : void {
        $data = [
            'name' => 'Test',
            'description' => 'Test user role',
            'adminLevel' => '1',
        ];

        $command = CreateRoleCommand::fromRequest($data);

        self::assertSame(1, $command->adminLevel);
    }
}
