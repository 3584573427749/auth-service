<?php

declare(strict_types=1);
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();
$env = getenv('APP_ENV') ?: 'development';

return [
    'paths' => [
        'migrations' => 'migrations',
        'seeds' => 'seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => $env,
        $env => [
            'adapter' => 'mysql',
            'host' => getenv('DB_HOST'),
            'name' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'pass' => getenv('DB_PASS'),
            'port' => (int)getenv('DB_PORT') ?: 3307,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],
];
