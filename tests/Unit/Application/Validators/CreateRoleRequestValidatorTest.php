<?php

declare(strict_types=1);

namespace Tests\Validators\CreateUserRequestValidator;

namespace Tests\Unit\Application\Validators;

use App\Application\Validators\CreateRoleRequestValidator;
use PHPUnit\Framework\TestCase;

final class CreateRoleRequestValidatorTest extends TestCase {
    public function testValidDataReturnsNoErrors(): void {
        $data = [
            'name' => 'Test',
            'description' => 'Test role',
            'adminLevel' => 1,
        ];

        $errors = CreateRoleRequestValidator::validate($data);

        self::assertSame([], $errors);
    }

    public function testMissingName(): void {
        $data = [
            'description' => 'Test role',
            'adminLevel' => 1,
        ];

        $errors = CreateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('name', $errors);
        self::assertSame('Name is required.', $errors['name']);
    }

    public function testMissingDescription(): void {
        $data = [
            'name' => 'Test',
            'adminLevel' => 1,
        ];

        $errors = CreateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('description', $errors);
        self::assertSame('Description is required.', $errors['description']);
    }

    public function testMissingAdminLevel(): void {
        $data = [
            'name' => 'Test',
            'description' => 'Test role',
        ];

        $errors = CreateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('adminLevel', $errors);
        self::assertSame('AdminLevel is required.', $errors['adminLevel']);
    }

    public function testNameTooLong(): void {
        $data = [
            'name' => str_repeat('A', 101),
            'description' => 'Test role',
            'adminLevel' => 1,
        ];

        $errors = CreateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('name', $errors);
        self::assertSame('Name is too long (max 100 characters).', $errors['name']);
    }

    public function testAdminLevelIsOutOfRange(): void {
        $data = [
            'name' => 'Test',
            'description' => 'Test role',
            'adminLevel' => 101,
        ];

        $errors = CreateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('adminLevel', $errors);
        self::assertSame('AdminLevel must be between 0 and 100.', $errors['adminLevel']);

        $data = [
            'name' => 'Test',
            'description' => 'Test role',
            'adminLevel' => -1,
        ];

        $errors = CreateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('adminLevel', $errors);
        self::assertSame('AdminLevel must be between 0 and 100.', $errors['adminLevel']);
    }

    public function testTooManyFields(): void {
        $data = [
            'name' => 'Test',
            'description' => 'Test role',
            'adminLevel' => 1,
            'extra' => 'not allowed',
        ];

        $errors = CreateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('tooManyFields', $errors);
        self::assertSame('Too many fields.', $errors['tooManyFields']);
    }

    public function testMultipleErrors(): void {
        $data = [
            'name' => 'Test',
            'description' => 'Test role',
            'adminLevel' => 101,
            'extra' => 'x',
        ];

        $errors = CreateRoleRequestValidator::validate($data);

        self::assertCount(2, $errors);

        self::assertSame('AdminLevel must be between 0 and 100.', $errors['adminLevel']);
        self::assertSame('Too many fields.', $errors['tooManyFields']);
    }
}
