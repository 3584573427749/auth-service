<?php

declare(strict_types=1);

namespace App\Application\Commands\Role;

class CreateRoleCommand {
    private function __construct(public string $name, public string $description, public int $adminLevel) {

    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromRequest(array $data) : self {
        // Normalisera
        $name = $data['name']
                |> trim(...)
                |> mb_ucfirst(...);
        $description = trim($data['description']);
        $adminLevel = filter_var($data['adminLevel'], FILTER_VALIDATE_INT);

        return new self($name, $description, (int) $adminLevel);
    }
}
