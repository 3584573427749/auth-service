<?php
declare(strict_types=1);

use App\Application\ErrorHandler\ErrorHandler;
use App\Application\ErrorHandler\ErrorMiddleware;
use App\Application\Middleware\AuthServiceMiddleware;
use App\Application\Settings;
use App\Infrastructure\Auth\AuthServiceClient;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;

return function(ContainerBuilder $builder) {
    $builder->addDefinitions(
        [Settings::class => fn() => Settings::getInstance(),
            'logger' => fn() => (require __DIR__ . '/logger.php')(),
            ErrorHandler::class => fn($c) => new ErrorHandler($c->get('logger')),
            ErrorMiddleware::class => fn($c) => new ErrorMiddleware($c->get(ErrorHandler::class)),

            // Database Connection (singleton)
            Connection::class => function(ContainerInterface $c) {
                $connectionParams = [
                    'dbname' => $_ENV['DB_NAME'],
                    'user' => $_ENV['DB_USER'],
                    'password' => $_ENV['DB_PASSWORD'],
                    'host' => $_ENV['DB_HOST'],
                    'port' => $_ENV['DB_PORT'] ?? 3306,
                    'driver' => 'pdo_mysql',
                    'charset' => 'utf8mb4',
                ];

                return DriverManager::getConnection($connectionParams);
            },

            AuthServiceClient::class => fn($c) => new AuthServiceClient($c->get(App\Application\Settings::class)),

            AuthServiceMiddleware::class => fn($c) => new AuthServiceMiddleware(
                $c->get(AuthServiceClient::class),
                $c->get(App\Application\Settings::class)
            ),

        ]);
};
