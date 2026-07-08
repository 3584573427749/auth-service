<?php

declare(strict_types=1);

namespace App\Application\Validators;

use DateMalformedStringException;

class UpdateRoleRequestValidator {
    /**
     * @param array<string, mixed> $data
     * @return string[]
     */
    public static function validate(array $data): array {
        $errors = [];

        if (!isset($data['id'])) {
            $errors['id'] = 'Id is required.';
        } elseif ($data['id'] !== $data['roleId']) {
            $errors['id'] = 'Ids does not match.';
        }
        if (!isset($data['name'])) {
            $errors['name'] = 'Name is required.';
        } elseif (mb_strlen($data['name']) > 100) {
            $errors['name'] = 'Name is too long.';
        }

        if (!isset($data['description'])) {
            $errors['description'] = 'Description is required.';
        } elseif (mb_strlen($data['description']) > 255) {
            $errors['description'] = 'Description is too long.';
        }

        if (!isset($data['adminLevel'])) {
            $errors['adminLevel'] = 'Admin level is required.';
        } elseif (!is_int($data['adminLevel'])) {
            $errors['adminLevel'] = 'Admin level must be an integer.';
        } elseif ((int)$data['adminLevel'] < 0 || (int)$data['adminLevel'] > 100) {
            $errors['adminLevel'] = 'AdminLevel must be between 0 and 100.';
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

        if (count($data) > 7) {
            $errors['tooManyFields'] = 'Too many fields.';
        }

        return $errors;
    }
}
