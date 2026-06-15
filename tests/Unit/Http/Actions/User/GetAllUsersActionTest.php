<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\User;

use App\Application\Handlers\User\GetUserHandler;
use App\Http\Actions\User\GetAllUsersAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class GetAllUsersActionTest extends TestCase {
    public function testReturnsAllUsers() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $users = [
            [
                'userId' => '1',
                'email' => 'a@test.com',
                'firstName' => 'A',
                'lastName' => 'User',
            ],
            [
                'userId' => '2',
                'email' => 'b@test.com',
                'firstName' => 'B',
                'lastName' => 'User',
            ],
        ];

        $handler = $this->createMock(GetUserHandler::class);

        $handler
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($users);

        $action = new GetAllUsersAction($logger, $handler);

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/users');

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertArrayHasKey('data', $payload);

        self::assertIsArray($payload['data']);

        self::assertCount(2, $payload['data']);

        self::assertSame('a@test.com', $payload['data'][0]['email']);
        self::assertSame('b@test.com', $payload['data'][1]['email']);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array<string, mixed>
     */
    private function decodeJsonResponse($response) : array {
        $body = (string) $response->getBody();

        self::assertNotSame('', $body);

        $decoded = json_decode($body, true);

        self::assertIsArray($decoded);

        return $decoded;
    }
}
