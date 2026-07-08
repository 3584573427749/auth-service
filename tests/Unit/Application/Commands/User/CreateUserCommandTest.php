<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Commands\User;

use App\Application\Commands\User\CreateUserCommand;
use PHPUnit\Framework\TestCase;

final class CreateUserCommandTest extends TestCase {
    public function testFromRequestMapsValuesCorrectly() : void {
        $data = [
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'Name',
        ];

        $command = CreateUserCommand::fromRequest($data);

        self::assertSame('test@example.com', $command->email);
        self::assertSame('User', $command->firstName);
        self::assertSame('Name', $command->lastName);
    }

    public function testEmailIsTrimmedAndLowercased() : void {
        $data = [
            'email' => '  TEST@EXAMPLE.COM  ',
            'firstName' => 'User',
            'lastName' => 'Name',
        ];

        $command = CreateUserCommand::fromRequest($data);

        self::assertSame('test@example.com', $command->email);
    }

    public function testNamesAreTrimmed() : void {
        $data = [
            'email' => 'test@example.com',
            'firstName' => '  User  ',
            'lastName' => '  Name  ',
        ];

        $command = CreateUserCommand::fromRequest($data);

        self::assertSame('User', $command->firstName);
        self::assertSame('Name', $command->lastName);
    }

    public function testHandlesMinimalValidInput() : void {
        $data = [
            'email' => 'a@b.c',
            'firstName' => 'A',
            'lastName' => 'B',
        ];

        $command = CreateUserCommand::fromRequest($data);

        self::assertSame('a@b.c', $command->email);
        self::assertSame('A', $command->firstName);
        self::assertSame('B', $command->lastName);
    }
}
