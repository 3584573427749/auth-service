<?php

declare(strict_types=1);

namespace Http\Actions\User;

use App\Application\Handlers\User\GetUserHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\User\GetUserAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\OpenApi\OpenApiValidator;

final class GetUserActionTest extends TestCase {
    public function testReturnsUserDTO() : void {
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
        $dto= UserDTO::fromUser($user);

        $handler = $this->createMock(GetUserHandler::class);

        $handler
            ->expects($this->once())
            ->method('getById')
            ->willReturn($dto);

        $action = new GetUserAction($logger, $handler);

        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/users/550e8400-e29b-41d4-a716-446655440000');

        $response = new ResponseFactory()->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertArrayHasKey('data', $payload);

        self::assertIsArray($payload['data']);

        self::assertSame('a@test.com', $payload['data']['email']);

        $validator = new OpenApiValidator();
        $validator->validateResponse('/users/{id}', 'GET', $result);
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
