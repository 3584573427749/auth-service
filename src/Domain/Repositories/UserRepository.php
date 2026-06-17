<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\UserId;

interface UserRepository {
    public function existsByEmail(string $email) : bool;

    public function save(User $user) : void;

    /**
     * @return User[]
     */
    public function getAll() : array;

    public function getById(UserId $id) : User;
}
