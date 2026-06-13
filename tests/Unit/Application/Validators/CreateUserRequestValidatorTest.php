<?php

declare(strict_types=1);

namespace Tests\Validators\CreateUserRequestValidator;

namespace Tests\Unit\Application\Validators;

use App\Application\Validators\CreateUserRequestValidator;
use PHPUnit\Framework\TestCase;

final class CreateUserRequestValidatorTest extends TestCase {
    public function testValidDataReturnsNoErrors() : void {
        $data = [
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'Name',
        ];

        $errors = CreateUserRequestValidator::validate($data);

        self::assertSame([], $errors);
    }

    public function testMissingEmail() : void {
        $data = [
            'firstName' => 'User',
            'lastName' => 'Name',
        ];

        $errors = CreateUserRequestValidator::validate($data);

        self::assertArrayHasKey('email', $errors);
        self::assertSame('Email is required.', $errors['email']);
    }

    public function testInvalidEmail() : void {
        $data = [
            'email' => 'invalid-email',
            'firstName' => 'User',
            'lastName' => 'Name',
        ];

        $errors = CreateUserRequestValidator::validate($data);

        self::assertArrayHasKey('email', $errors);
        self::assertSame('Email is invalid.', $errors['email']);
    }

    public function testMissingFirstName() : void {
        $data = [
            'email' => 'test@example.com',
            'lastName' => 'Name',
        ];

        $errors = CreateUserRequestValidator::validate($data);

        self::assertArrayHasKey('firstName', $errors);
        self::assertSame('First name is required.', $errors['firstName']);
    }

    public function testFirstNameTooShort() : void {
        $data = [
            'email' => 'test@example.com',
            'firstName' => 'A',
            'lastName' => 'Name',
        ];

        $errors = CreateUserRequestValidator::validate($data);

        self::assertArrayHasKey('firstName', $errors);
        self::assertSame('First name must be at least 2 characters.', $errors['firstName']);
    }

    public function testMissingLastName() : void {
        $data = [
            'email' => 'test@example.com',
            'firstName' => 'User',
        ];

        $errors = CreateUserRequestValidator::validate($data);

        self::assertArrayHasKey('lastName', $errors);
        self::assertSame('Last name is required.', $errors['lastName']);
    }

    public function testLastNameTooShort() : void {
        $data = [
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'A',
        ];

        $errors = CreateUserRequestValidator::validate($data);

        self::assertArrayHasKey('lastName', $errors);
        self::assertSame('Last name must be at least 2 characters.', $errors['lastName']);
    }

    public function testTooManyFields() : void {
        $data = [
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'Name',
            'extra' => 'not allowed',
        ];

        $errors = CreateUserRequestValidator::validate($data);

        self::assertArrayHasKey('tooManyFields', $errors);
        self::assertSame('Too many fields.', $errors['tooManyFields']);
    }

    public function testMultipleErrors() : void {
        $data = [
            'email' => 'invalid',
            'firstName' => 'A',
            'lastName' => 'B',
            'extra' => 'x',
        ];

        $errors = CreateUserRequestValidator::validate($data);

        self::assertCount(4, $errors);

        self::assertSame('Email is invalid.', $errors['email']);
        self::assertSame('First name must be at least 2 characters.', $errors['firstName']);
        self::assertSame('Last name must be at least 2 characters.', $errors['lastName']);
        self::assertSame('Too many fields.', $errors['tooManyFields']);
    }
}
