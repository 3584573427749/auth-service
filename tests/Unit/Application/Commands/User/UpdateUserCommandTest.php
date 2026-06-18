<?php

declare(strict_types=1);

namespace Application\Commands\User;

use App\Application\Commands\User\CreateUserCommand;
use App\Application\Commands\User\UpdateUserCommand;
use PHPUnit\Framework\TestCase;

final class UpdateUserCommandTest extends TestCase {
    public function testFromRequestMapsValuesCorrectly() : void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'Name',
            'isActive' => true,
            'createdAt' => '2026-01-01 10:00:00',
        ];
        ;
        $command = UpdateUserCommand::fromRequest($data);

        self::assertSame('test@example.com', $command->email);
        self::assertSame('User', $command->firstName);
        self::assertSame('Name', $command->lastName);
    }

    public function testEmailIsTrimmedAndLowercased() : void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => '        TEST@EXAMPLE.COM',
            'firstName' => 'User',
            'lastName' => 'Name',
            'isActive' => true,
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $command = CreateUserCommand::fromRequest($data);

        self::assertSame('test@example.com', $command->email);
    }

    public function testNamesAreTrimmed() : void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => '       User         ',
            'lastName' => '       Name         ',
            'isActive' => true,
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $command = UpdateUserCommand::fromRequest($data);

        self::assertSame('User', $command->firstName);
        self::assertSame('Name', $command->lastName);
    }

    public function testHandlesMinimalValidInput() : void {
        $data = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'a@b.c',
            'firstName' => 'A',
            'lastName' => 'B',
            'isActive' => true,
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $command = UpdateUserCommand::fromRequest($data);

        self::assertSame('a@b.c', $command->email);
        self::assertSame('A', $command->firstName);
        self::assertSame('B', $command->lastName);
    }
}
