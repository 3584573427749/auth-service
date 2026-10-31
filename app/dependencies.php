<?php

declare(strict_types=1);

use App\Application\Clients\GroupService\GroupServiceClient;
use App\Application\ErrorHandler\ErrorHandler;
use App\Application\Middleware\ErrorMiddleware;
use App\Application\Services\JwtService;
use App\Http\Clients\GroupService\HttpGroupServiceClient;
use App\Infrastructure\Services\FirebaseJwtService;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $builder) {

    $builder->addDefinitions([
        'logger' => fn () => (require __DIR__ . '/logger.php')(),
        LoggerInterface::class => fn ($c) => $c->get('logger'),

        ErrorHandler::class => fn ($c) => new ErrorHandler($c->get('logger')),
        ErrorMiddleware::class => fn ($c) => new ErrorMiddleware($c->get(ErrorHandler::class)),

        // Database Connection (singleton)
        Connection::class => function (ContainerInterface $c) {
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

        ClientInterface::class => static function () : ClientInterface {
            return new Client();
        },

        // GroupServiceClient binding
        GroupServiceClient::class => function (
            ContainerInterface $c,
        ) {
            return new HttpGroupServiceClient(
                $c->get(LoggerInterface::class),
                $c->get(ClientInterface::class),
                $_ENV['GROUP_SERVICE_URL'],
            );
        },
        // JWT-service binding
        JwtService::class => static function () : JwtService {
            return new FirebaseJwtService(
                $_ENV['JWT_SECRET'],
                $_ENV['JWT_ISSUER'],
                (int)$_ENV['JWT_ACCESS_TOKEN_LIFETIME'],
                (int)$_ENV['JWT_REFRESH_TOKEN_LIFETIME'],
            );
        },
    ]);
};
