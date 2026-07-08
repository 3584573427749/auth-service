<?php

declare(strict_types=1);

namespace Tests\Validators\CreateUserRequestValidator;

namespace Tests\Unit\Application\Validators;

use App\Application\Validators\UpdateRoleRequestValidator;
use PHPUnit\Framework\TestCase;

final class UpdateRoleRequestValidatorTest extends TestCase {
    public function testValidDataReturnsNoErrors(): void {
        $data = [
            'roleId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'User',
            'description' => 'Test Role',
            'adminLevel' => 1,
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateRoleRequestValidator::validate($data);

        self::assertSame([], $errors);
    }

    public function testMissingName(): void {
        $data = [
            'roleId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'description' => 'Test Role',
            'adminLevel' => '1',
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('name', $errors);
        self::assertSame('Name is required.', $errors['name']);
    }

    public function testInvalidName(): void {
        $data = [
            'roleId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => str_repeat('A', 101),
            'description' => 'Test Role',
            'adminLevel' => '1',
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('name', $errors);
        self::assertSame('Name is too long.', $errors['name']);
    }

    public function testMissingDescription(): void {
        $data = [
            'roleId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'User',
            'adminLevel' => '1',
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('description', $errors);
        self::assertSame('Description is required.', $errors['description']);
    }

    public function testAdminLevelInvalid(): void {
        $data = [
            'roleId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'User',
            'description' => 'Test Role',
            'adminLevel' => 101,
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('adminLevel', $errors);
        self::assertSame('AdminLevel must be between 0 and 100.', $errors['adminLevel']);

        $data = [
            'roleId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'User',
            'description' => 'Test Role',
            'adminLevel' => -1,
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('adminLevel', $errors);
        self::assertSame('AdminLevel must be between 0 and 100.', $errors['adminLevel']);
    }

    public function testCreatedAtInvalid(): void {
        $data = [
            'roleId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'User',
            'description' => 'Test Role',
            'adminLevel' => '1',
            'createdAt' => 'Invalid',
        ];

        $errors = UpdateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('createdAt', $errors);
        self::assertSame('Invalid created date.', $errors['createdAt']);
    }

    public function testUpdatedAtInvalid(): void {
        $data = [
            'roleId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'User',
            'description' => 'Test Role',
            'adminLevel' => '1',
            'createdAt' => '2026-01-01 10:00:00',
            'updatedAt' => 'Invalid',
        ];

        $errors = UpdateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('updatedAt', $errors);
        self::assertSame('Invalid updated date.', $errors['updatedAt']);
    }

    public function testTooManyFields(): void {
        $data = [
            'roleId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'User',
            'description' => 'Test Role',
            'adminLevel' => '1',
            'createdAt' => '2026-01-01 10:00:00',
            'updatedAt' => 'Invalid',
            'extra' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateRoleRequestValidator::validate($data);

        self::assertArrayHasKey('tooManyFields', $errors);
        self::assertSame('Too many fields.', $errors['tooManyFields']);
    }

    public function testMultipleErrors(): void {
        $data = [
            'roleId' => '550e8400-e29b-41d4-a716-446655440011',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'description' => 'b',
            'adminLevel' => '1',
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateRoleRequestValidator::validate($data);

        self::assertCount(3, $errors);

        self::assertSame('Ids does not match.', $errors['id']);
        self::assertSame('Name is required.', $errors['name']);
        self::assertSame('Admin level must be an integer.', $errors['adminLevel']);
    }
}
