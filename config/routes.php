<?php

declare(strict_types=1);

use App\Application\Actions\Health\HealthAction;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app): void {

    // Health
    $app->group('/health', function (RouteCollectorProxy $group) {
        $group->get('', HealthAction::class);
    });
};
