<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\User;

use App\Application\Handlers\User\GetUserHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\User\GetAllUsersAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\OpenApi\OpenApiValidator;

final class GetAllUsersActionTest extends TestCase {
    public function testReturnsAllUsers() : void {
        $logger = $this->createMock(LoggerInterface::class);
        $users = [];
        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('a@test.com'),
            'User',
            'Name',
            true,
            new DateTimeValue('2026-06-10 10:00:00'),
            null,
        );
        $users[] = UserDTO::fromUser($user);

        $user = new User(
            new UserId('660e8400-e29b-41d4-a716-446655440000'),
            new Email('b@test.com'),
            'User',
            'Name',
            true,
            new DateTimeValue('2026-06-10 10:00:00'),
            new DateTimeValue('2026-06-11 11:00:00'),
        );
        $users[] = UserDTO::fromUser($user);

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

        $validator = new OpenApiValidator();
        $validator->validateResponse('/users', 'GET', $result);

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
