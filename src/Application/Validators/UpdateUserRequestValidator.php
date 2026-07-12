<?php

declare(strict_types=1);

namespace App\Application\Validators;

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
        } elseif (mb_strlen($data['firstName']) < 2 || mb_strlen($data['firstName']) > 100) {
            $errors['firstName'] = 'First name must be between 2 and 100 characters.';
        }

        if (!isset($data['lastName'])) {
            $errors['lastName'] = 'Last name is required.';
        } elseif (mb_strlen($data['lastName']) < 2 || mb_strlen($data['lastName']) > 100) {
            $errors['lastName'] = 'Last name must be between 2 and 100 characters.';
        }

        if (!isset($data['roles']) || !is_array($data['roles'])) {
            $errors['roles'] = 'Roles must be an array.';
        } else {
            foreach ($data['roles'] as $roleId) {
                if (!is_string($roleId)) {
                    $errors['roles'] = 'Roles must be an array of strings.';
                    break;
                }
            }
        }


        if (count($data) > 9) {
            $errors['tooManyFields'] = 'Too many fields.';
        }

        return $errors;
    }
}
