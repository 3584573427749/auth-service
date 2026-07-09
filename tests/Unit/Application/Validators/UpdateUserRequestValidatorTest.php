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
            'createdAt' => '2026-01-01 10:00:00',
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
            'createdAt' => '2026-01-01 10:00:00',
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
            'createdAt' => '2026-01-01 10:00:00',
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
            'createdAt' => '2026-01-01 10:00:00',
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
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('firstName', $errors);
        self::assertSame('First name must be at least 2 characters.', $errors['firstName']);
    }

    public function testMissingLastName() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'createdAt' => '2026-01-01 10:00:00',
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
            'createdAt' => '2026-01-01 10:00:00',
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('lastName', $errors);
        self::assertSame('Last name must be at least 2 characters.', $errors['lastName']);
    }

    public function testMissingCreatedAt() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'b',
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('createdAt', $errors);
        self::assertSame('Created at is required.', $errors['createdAt']);
    }

    public function testCreatedAtInvalid() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'b',
            'createdAt' => 'Invalid',
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('createdAt', $errors);
        self::assertSame('Invalid created date.', $errors['createdAt']);
    }

    public function testDeletedAtInvalid() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'b',
            'createdAt' => '2026-01-01 10:00:00',
            'deletedAt' => 'Invalid',
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('deletedAt', $errors);
        self::assertSame('Invalid deleted date.', $errors['deletedAt']);
    }

    public function testUpdatedAtInvalid() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'b',
            'createdAt' => '2026-01-01 10:00:00',
            'updatedAt' => 'Invalid',
            'deletedAt' => null,
        ];

        $errors = UpdateUserRequestValidator::validate($data);

        self::assertArrayHasKey('updatedAt', $errors);
        self::assertSame('Invalid updated date.', $errors['updatedAt']);
    }

    public function testTooManyFields() : void {
        $data = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'Name',
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

        self::assertCount(4, $errors);

        self::assertSame('Ids does not match.', $errors['id']);
        self::assertSame('Email is invalid.', $errors['email']);
        self::assertSame('First name must be at least 2 characters.', $errors['firstName']);
        self::assertSame('Last name must be at least 2 characters.', $errors['lastName']);
    }
}
