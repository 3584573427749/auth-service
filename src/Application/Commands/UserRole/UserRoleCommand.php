<?php

declare(strict_types=1);

namespace App\Application\Commands\UserRole;

class UserRoleCommand {
    private function __construct(public string $userId, public string $roleId) {

    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromRequest(array $data) : self {
        // Normalisera
        $userId = $data['userId'];
        $roleId = $data['roleId'];

        return new self($userId, $roleId);
    }
}
