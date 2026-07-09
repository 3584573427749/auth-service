<?php

declare(strict_types=1);

namespace App\Http\Actions\Role;

use App\Http\Actions\Action;
use Psr\Log\LoggerInterface;

abstract class RoleAction extends Action {
    public function __construct(LoggerInterface $logger) {
        parent::__construct($logger);
    }
}
