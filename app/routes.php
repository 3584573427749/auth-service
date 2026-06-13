<?php

declare(strict_types=1);

use App\Http\Actions\User\CreateUserAction;
use Slim\App;

return function (App $app) : void {

    $app->post('/users', CreateUserAction::class);
};
