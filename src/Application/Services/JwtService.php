<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\Role;
use App\Domain\Entities\User;

interface JwtService {
    /**
     * @param array<int, Role> $roles
     */
    public function createAccessToken(User $user, array $roles) : string;

    public function createRefreshToken(User $user) : string;

    /**
     * @return array<string, mixed>
     */
    public function verify(string $token) : array;

    public function isAccessToken(string $token) : bool;

    public function isRefreshToken(string $token) : bool;
}
