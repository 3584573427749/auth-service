<?php

declare(strict_types=1);

namespace App\Application\Validators;

class CreateRoleRequestValidator {
    /**
     * @param array<string, mixed> $data
     * @return string[]
     */
    public static function validate(array $data) : array {
        $errors = [];

        if (!isset($data['name'])) {
            $errors['name'] = 'Name is required.';
        } elseif (mb_strlen($data['name']) > 100) {
            $errors['name'] = 'Name is too long (max 100 characters).';
        }

        if (!isset($data['description'])) {
            $errors['description'] = 'Description is required.';
        } elseif (mb_strlen($data['description']) > 255) {
            $errors['description'] = 'Description is too long (max 255 characters).';
        }

        if (!isset($data['adminLevel'])) {
            $errors['adminLevel'] = 'AdminLevel is required.';
        } elseif (filter_var($data['adminLevel'], FILTER_VALIDATE_INT) === false) {
            $errors['adminLevel'] = 'AdminLevel must be a valid integer.';
        } elseif ((int)$data['adminLevel'] < 0 || (int)$data['adminLevel'] > 100) {
            $errors['adminLevel'] = 'AdminLevel must be between 0 and 100.';
        }

        if (count($data) > 3) {
            $errors['tooManyFields'] = 'Too many fields.';
        }

        return $errors;
    }
}
