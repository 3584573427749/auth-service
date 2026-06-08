<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\User;

interface UserRepository {
    public function existsByEmail(string $email):bool;
    public function save(User $user):void;
}