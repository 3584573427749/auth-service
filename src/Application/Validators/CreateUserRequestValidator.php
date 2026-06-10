<?php

declare(strict_types=1);

namespace App\Application\Validators;

class CreateUserRequestValidator
{
    /**
     * @param string[] $data
     * @return string[]
     */
    public static function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is invalid.';
        }

        if (!isset($data['firstName'])) {
            $errors['firstName'] = 'First name is required.';
        } elseif (strlen($data['firstName']) < 2) {
            $errors['firstName'] = 'First name must be at least 2 characters.';
        }

        if (!isset($data['lastName'])) {
            $errors['lastName'] = 'Last name is required.';
        } elseif (strlen($data['lastName']) < 2) {
            $errors['lastName'] = 'Last name must be at least 2 characters.';
        }

        if (count($data) > 3) {
            $errors['tooManyFields'] = 'Too many fields.';
        }

        return $errors;
    }
}
