<?php
use App\Application\Settings; use App\Application\ErrorHandler\ErrorMiddleware; use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Initiera Settings (läser .env)
Settings::getInstance();

$container = require __DIR__ . '/../config/di.php';
AppFactory::setContainer($container);
$app = AppFactory::create();

// ErrorMiddleware först
$app->add(ErrorMiddleware::class);

(require __DIR__ . '/../config/middleware.php')($app);

(require __DIR__ . '/../config/routes.php')($app);

$app->run();

