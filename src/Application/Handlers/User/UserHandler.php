<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Domain\Repositories\UserRepository;
use Doctrine\DBAL\Connection;

abstract class UserHandler
{
    public function __construct(protected Connection $db, protected UserRepository $userRepository)
    {

    }
}
