<?php

declare(strict_types=1);

use Slim\App;

return function (App $app) : void {
    // JSON body parsing
    $app->addBodyParsingMiddleware();
};
