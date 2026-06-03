<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Application\Settings;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DriverManager;

final class Connection {
    private static ?Connection $instance = null;
    private DBALConnection $connection;

    private function __construct(Settings $settings) {
        $params = [
            'dbname' => $settings->get('DB_NAME'),
            'user' => $settings->get('DB_USER'),
            'password' => $settings->get('DB_PASS'),
            'host' => $settings->get('DB_HOST'),
            'port' => $settings->get('DB_PORT', 3306),
            'driver' => 'pdo_mysql',
            'charset' => 'utf8mb4',
        ];

        // Lazy connection: ansluter först vid första query
        $this->connection = DriverManager::getConnection($params);
    }

    public static function getInstance(Settings $settings): self {
        if (self::$instance === null) {
            self::$instance = new self($settings);
        }

        return self::$instance;
    }

    public function get(): DBALConnection {
        return $this->connection;
    }
}
