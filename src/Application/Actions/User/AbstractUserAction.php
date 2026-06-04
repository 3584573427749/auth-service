<?php

namespace App\Application\Actions\User;

use App\Application\Actions\AbstractAction;

class AbstractUserAction extends AbstractAction {

    public function __construct(protected UserRepository $repository) {}

}