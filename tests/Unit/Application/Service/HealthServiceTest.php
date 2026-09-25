<?php


declare(strict_types=1);

namespace Tests\Unit\Application\Services;

use App\Application\Services\HealthService;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class HealthServiceTest extends TestCase {
    public function testReturnsOkWhenDatabaseIsReachable() : void {
        $connection = $this->createMock(Connection::class);

        $connection
            ->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT 1');

        $service = new HealthService($connection);

        $dto = $service->getHealth();

        $data = $dto->jsonSerialize();

        self::assertSame('ok', $data['status']);
        self::assertSame('auth-service', $data['service']);
        self::assertArrayHasKey('database', $data['checks']);
        self::assertSame('ok', $data['checks']['database']);
    }

    public function testReturnsDownWhenDatabaseIsNotReachable() : void {
        $connection = $this->createMock(Connection::class);

        $connection
            ->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT 1')
            ->willThrowException(
                new \RuntimeException('Database unavailable'),
            );

        $service = new HealthService($connection);

        $dto = $service->getHealth();

        $data = $dto->jsonSerialize();

        self::assertSame(
            'degraded',
            $data['status'],
        );

        self::assertArrayHasKey(
            'database',
            $data['checks'],
        );

        self::assertSame(
            'down',
            $data['checks']['database'],
        );
    }
}
