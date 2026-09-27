<?php


declare(strict_types=1);

namespace Tests\Unit\Http\Clients\GroupService;

use App\Domain\Exception\GroupServiceUnavailableException;
use App\Domain\ValueObjects\UserId;
use App\Http\Clients\GroupService\HttpGroupServiceClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class HttpGroupServiceClientTest extends TestCase {
    public function testSyncLeaderSendsRequest() : void {

        $httpClient = $this->createMock(ClientInterface::class);

        $logger = $this->createMock(LoggerInterface::class);

        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'http://group-service/users',
                [
                    'json' => [
                        'id' => '550e8400-e29b-41d4-a716-446655440000',
                        'firstName' => 'Test',
                        'lastName' => 'User',
                        'active' => 1,
                    ],
                ],
            );

        $client = new HttpGroupServiceClient(
            $logger,
            $httpClient,
            'http://group-service',
        );

        $client->syncLeader(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            'Test',
            'User',
            true,
        );
    }

    public function testSyncLeaderThrowsGroupServiceUnavailableException() : void {
        $httpClient = $this->createMock(ClientInterface::class);

        $logger = $this->createMock(LoggerInterface::class);

        $httpClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException(
                new ConnectException(
                    'Boom',
                    new Request(
                        'POST',
                        'http://group-service/users',
                    ),
                ),
            );
        $logger
            ->expects($this->once())
            ->method('error');

        $client = new HttpGroupServiceClient(
            $logger,
            $httpClient,
            'http://group-service',
        );

        $this->expectException(
            GroupServiceUnavailableException::class,
        );

        $client->syncLeader(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            'Test',
            'User',
            true,
        );
    }
}
