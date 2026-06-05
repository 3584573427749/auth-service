<?php

namespace App\Domain\Repositories;

interface UserRepository {
    public function existsByEmail(string $email):bool;
    public function save(User $user):void;
}