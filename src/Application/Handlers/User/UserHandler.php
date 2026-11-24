<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Domain\Repositories\UserRepository;

abstract class UserHandler {
    public function __construct(protected UserRepository $repository) {

    }
}
