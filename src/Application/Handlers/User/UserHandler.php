<?php

namespace App\Application\Handlers\User;

use App\Domain\Repositories\UserRepository;
use Doctrine\DBAL\Connection;

abstract class UserHandler {
    /**
     * @param Connection $db
     * @param UserRepository $userRepository
     */
    public function __construct(protected Connection $db, protected UserRepository $userRepository) {

    }
}