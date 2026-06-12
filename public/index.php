<?php

use App\Application\Middleware\ErrorMiddleware;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';


// Bygg containern korrekt
$containerBuilder = new ContainerBuilder();
(require __DIR__ . '/../app/dependencies.php')($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

// ErrorMiddleware först
$app->add(ErrorMiddleware::class);

(require __DIR__ . '/../app/middleware.php')($app);
(require __DIR__ . '/../app/routes.php')($app);

$app->run();