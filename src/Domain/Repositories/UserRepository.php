<?php

namespace App\Domain\Repositories;

interface UserRepository {
    public function existsByEmail(string $email):bool;
    public function add(User $user):void;
}