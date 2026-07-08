<?php

declare(strict_types=1);

namespace App\Application\Validators;

use DateMalformedStringException;

class UpdateUserRequestValidator {
    /**
     * @param array<string, mixed> $data
     * @return string[]
     */
    public static function validate(array $data) : array {
        $errors = [];

        if (!isset($data['id'])) {
            $errors['id'] = 'Id is required.';
        } elseif ($data['id'] !== $data['userId']) {
            $errors['id'] = 'Ids does not match.';
        }
        if (!isset($data['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is invalid.';
        }

        if (!isset($data['firstName'])) {
            $errors['firstName'] = 'First name is required.';
        } elseif (mb_strlen($data['firstName']) < 2) {
            $errors['firstName'] = 'First name must be at least 2 characters.';
        }

        if (!isset($data['lastName'])) {
            $errors['lastName'] = 'Last name is required.';
        } elseif (mb_strlen($data['lastName']) < 2) {
            $errors['lastName'] = 'Last name must be at least 2 characters.';
        }

        try {
            if (!isset($data['createdAt'])) {
                $errors['createdAt'] = 'Created at is required.';
            } else {
                $createdAt = new \DateTimeImmutable($data['createdAt']);
            }
        } catch (DateMalformedStringException $e) {
            $errors['createdAt'] = 'Invalid created date.';
        }
        try {
            if (isset($data['updatedAt'])) {
                $updatedAt = new \DateTimeImmutable($data['updatedAt']);
            }
        } catch (DateMalformedStringException $e) {
            $errors['updatedAt'] = 'Invalid updated date.';
        }

        try {
            if (isset($data['deletedAt'])) {
                $deletedAt = new \DateTimeImmutable($data['deletedAt']);
            }
        } catch (DateMalformedStringException $e) {
            $errors['deletedAt'] = 'Invalid deleted date.';
        }

        if (count($data) > 8) {
            $errors['tooManyFields'] = 'Too many fields.';
        }

        return $errors;
    }
}
