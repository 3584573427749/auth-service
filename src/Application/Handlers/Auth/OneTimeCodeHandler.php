<?php

declare(strict_types=1);

namespace App\Application\Handlers\Auth;

use App\Domain\Repositories\OneTimeCodeRepository;
use App\Domain\Repositories\UserRepository;

abstract class OneTimeCodeHandler {
    public function __construct(protected UserRepository $userRepository, protected OneTimeCodeRepository $oneTimeCodeRepository) {

    }
}
