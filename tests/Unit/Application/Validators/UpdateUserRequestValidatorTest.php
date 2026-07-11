<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Validators;

use App\Application\Validators\UpdateUserRequestValidator;
use PHPUnit\Framework\TestCase;

final class UpdateUserRequestValidatorTest extends TestCase {
    public function testValidDataReturnsNoErrors() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'Name',
            'roles' => ['11111111-1111-1111-1111-111111111111'],
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertSame([], $errors);
    }

    public function testMissingEmail() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'firstName' => 'User',
            'lastName' => 'Name',
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('email', $errors);
        self::assertSame('Email is required.', $errors['email']);
    }

    public function testMissingId() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'Name',
            'roles' => [],
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('id', $errors);
        self::assertSame('Id is required.', $errors['id']);
    }

    public function testInvalidEmail() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'example.com',
            'firstName' => 'User',
            'lastName' => 'Name',
            'roles' => [],
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('email', $errors);
        self::assertSame('Email is invalid.', $errors['email']);
    }

    public function testMissingFirstName() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'lastName' => 'Name',
            'roles' => [],
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('firstName', $errors);
        self::assertSame('First name is required.', $errors['firstName']);
    }

    public function testFirstNameTooShort() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'a',
            'lastName' => 'Name',
            'roles' => [],
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('firstName', $errors);
        self::assertSame('First name must be between 2 and 100 characters.', $errors['firstName']);
    }

    public function testFirstNameTooLong() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => str_repeat('a', 101),
            'lastName' => 'Name',
            'roles' => [],
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('firstName', $errors);
        self::assertSame('First name must be between 2 and 100 characters.', $errors['firstName']);
    }

    public function testMissingLastName() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'roles' => [],
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('lastName', $errors);
        self::assertSame('Last name is required.', $errors['lastName']);
    }

    public function testLastNameTooShort() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'b',
            'roles' => [],
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('lastName', $errors);
        self::assertSame('Last name must be between 2 and 100 characters.', $errors['lastName']);
    }

    public function testLastNameTooLong() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => str_repeat('b', 101),
            'roles' => [],
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('lastName', $errors);
        self::assertSame('Last name must be between 2 and 100 characters.', $errors['lastName']);
    }

    public function testMissingRoles() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => str_repeat('b', 101),
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('roles', $errors);
        self::assertSame('Roles must be an array.', $errors['roles']);
    }

    public function testRolesIsNotArray() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => str_repeat('b', 101),
            'roles' => 'not_an_array',
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('roles', $errors);
        self::assertSame('Roles must be an array.', $errors['roles']);
    }

    public function testRolesIsInvalid() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => str_repeat('b', 101),
            'roles' => ['11111111-1111-1111-1111-111111111111', 1],
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('roles', $errors);
        self::assertSame('Roles must be an array of strings.', $errors['roles']);
    }

    public function testTooManyFields() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'Name',
            'roles' => [],
            'createdAt' => '2026-01-01 10:00:00',
            'updatedAt' => '2026-01-01 10:00:00',
            'deletedAt' => '2026-01-01 10:00:00',
            'extra' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('tooManyFields', $errors);
        self::assertSame('Too many fields.', $errors['tooManyFields']);
    }

    public function testMultipleErrors() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440011',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'example.com',
            'firstName' => 'a',
            'lastName' => 'b',
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertCount(5, $errors);

        self::assertSame('Ids does not match.', $errors['id']);
        self::assertSame('Email is invalid.', $errors['email']);
        self::assertSame('First name must be between 2 and 100 characters.', $errors['firstName']);
        self::assertSame('Last name must be between 2 and 100 characters.', $errors['lastName']);
        self::assertSame('Roles must be an array.', $errors['roles']);
    }
}
