<?php

declare(strict_types=1);

namespace Http\Actions\User;

use App\Application\Commands\User\UpdateUserCommand;
use App\Application\Handlers\User\UpdateUserHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\Exception\ValidationException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\User\UpdateUserAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class UpdateUserActionTest extends TestCase {
    public function testUpdatesUserAndReturns200WhenRequestBodyIsValid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('test@example.com'),
            'User',
            'Name',
            new DateTimeValue('2026-06-10T10:00:00+00:00'),
            new DateTimeValue('2026-06-10T10:00:00+00:00'),
            null,
        );

        $dto = UserDTO::fromUser($user)->withRoles(['11111111-1111-1111-1111-111111111111']);

        $handler = $this->createMock(UpdateUserHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(UpdateUserCommand::class))
            ->willReturn($dto);

        $action = new UpdateUserAction($logger, $handler);

        $request = new ServerRequestFactory()
            ->createServerRequest('PUT', '/users/550e8400-e29b-41d4-a716-446655440000')
            ->withAttribute('id', '550e8400-e29b-41d4-a716-446655440000')
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'firstName' => 'User',
                'lastName' => 'Name',
                'roles' => ['11111111-1111-1111-1111-111111111111'],
                'createdAt' => '2026-01-01T10:00:00+00:00',
                'updatedAt' => null,
                'deletedAt' => null,
            ]);

        $response = new ResponseFactory()->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(200, $payload['statusCode']);
        self::assertArrayHasKey('data', $payload);
        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $payload['data']['id']);
        self::assertSame('test@example.com', $payload['data']['email']);
        self::assertSame('User', $payload['data']['firstName']);
        self::assertSame('Name', $payload['data']['lastName']);
        self::assertArrayHasKey('roles', $payload['data']);
        self::assertSame(['11111111-1111-1111-1111-111111111111'], $payload['data']['roles']);
    }

    public function testUpdatesUserAndThrowsExceptionWhenRequestBodyIsInvalid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('test@example.com'),
            'User',
            'Name',
            new DateTimeValue('2026-06-10T10:00:00+00:00'),
            new DateTimeValue('2026-06-10T10:00:00+00:00'),
            null,
        );

        $dto = UserDTO::fromUser($user)->withRoles(['11111111-1111-1111-1111-111111111111']);

        $handler = $this->createMock(UpdateUserHandler::class);

        $handler
            ->expects($this->never())
            ->method('handle');

        $action = new UpdateUserAction($logger, $handler);

        $request = new ServerRequestFactory()
            ->createServerRequest('PUT', '/users/550e8400-e29b-41d4-a716-446655440000')
            ->withAttribute('id', '550e8400-e29b-41d4-a716-446655440000')
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'firstName' => 'User',
                'lastName' => 'Name',
                'roles' => ['11111111-1111-1111-1111-111111111111'],
                'createdAt' => '2026-01-01T10:00:00+00:00',
                'updatedAt' => null,
                'deletedAt' => null,
            ]);

        $response = new ResponseFactory()->createResponse();

        self::expectException(ValidationException::class);
        self::expectExceptionMessage('Felaktig indata');

        $action($request, $response, []);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(ResponseInterface $response) : array {
        $body = (string)$response->getBody();

        self::assertNotSame('', $body);

        $decoded = json_decode($body, true);

        self::assertIsArray($decoded);

        return $decoded;
    }
}
