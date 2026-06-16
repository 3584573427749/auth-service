<?php

declare(strict_types=1);

use App\Http\Actions\User\CreateUserAction;
use App\Http\Actions\User\GetAllUsersAction;
use Slim\App;

return function (App $app) : void {

    $app->post('/users', CreateUserAction::class);
    $app->get('/users', GetAllUsersAction::class);
};
