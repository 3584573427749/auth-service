<?php


declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\DataTransportObjects\Health\HealthDTO;
use Doctrine\DBAL\Connection;
use Throwable;

class HealthService {
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function getHealth() : HealthDTO {
        $checks = [];

        try {
            $this->connection->executeQuery('SELECT 1');

            $checks['database'] = 'ok';
        } catch (Throwable) {
            $checks['database'] = 'down';
        }

        $status = in_array('down', $checks, true)
            ? 'degraded'
            : 'ok';

        return new HealthDTO(
            $status,
            'auth-service',
            $this->getVersion(),
            $checks,
        );
    }

    private function getVersion() : string {
        return trim(
            file_get_contents(
                dirname(__DIR__, 3) . '/VERSION',
            ) ?: 'unknown',
        );
    }
}
