<?php

declare(strict_types=1);

use App\Application\Actions\Health\HealthAction;
use App\Application\Actions\User\CreateUserAction;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app): void {

    $app->post('/users', CreateUserAction::class);
};
