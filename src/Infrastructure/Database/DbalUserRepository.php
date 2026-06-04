<?php

namespace App\Infrastructure\Database;

use App\Domain\Repositories\User;
use App\Domain\Repositories\UserRepository;

class DbalUserRepository implements UserRepository {
    public function __construct(private Connection $connection) {

    }
    public function existsByEmail(string $email):bool {
       $dbQuery= $this->connection->
    }

    public function add(User $user):void {
        // TODO: Implement add() method.
    }
}