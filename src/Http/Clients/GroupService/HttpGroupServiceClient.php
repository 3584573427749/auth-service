<?php

declare(strict_types=1);

namespace App\Http\Clients\GroupService;

use App\Application\Clients\GroupService\GroupServiceClient;
use App\Domain\Exception\GroupServiceUnavailableException;
use App\Domain\ValueObjects\UserId;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class HttpGroupServiceClient implements GroupServiceClient {
    public function __construct(
        private LoggerInterface $logger,
        private ClientInterface $httpClient,
        private string $baseUrl,
    ) {
    }

    public function syncLeader(
        UserId $userId,
        string $firstName,
        string $lastName,
        bool $active,
    ) : void {
        try {
            $this->httpClient->request(
                'POST',
                "{$this->baseUrl}/users",
                [
                    'json' => [
                        'id' => $userId->toString(),
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'active' => $active ? 1 : 0,
                    ],
                ],
            );
        } catch (GuzzleException $e) {
            $this->logger->error(
                'Failed to synchronize leader with Group Service',
                [
                    'userId' => $userId->toString(),
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'active' => $active,
                    'error' => $e->getMessage(),
                ],
            );

            throw new GroupServiceUnavailableException(
                'Användaren kunde inte tilldelas rollen.',
                previous: $e,
            );
        }
    }
}
