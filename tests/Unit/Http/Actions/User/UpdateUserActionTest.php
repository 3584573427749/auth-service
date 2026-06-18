<?php

declare(strict_types=1);

namespace Http\Actions\User;

use App\Application\Commands\User\UpdateUserCommand;
use App\Application\Handlers\User\UpdateUserHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\User\UpdateUserAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\OpenApi\OpenApiValidator;

final class UpdateUserActionTest extends TestCase {
    public function testUpdatesUserAndReturns200WhenRequestBodyIsValid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('test@example.com'),
            'User',
            'Name',
            true,
            new DateTimeValue('2026-06-10 10:00:00'),
            new DateTimeValue('2026-06-11 10:00:00'),
        );

        $dto = UserDTO::fromUser($user);

        $handler = $this->createMock(UpdateUserHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(UpdateUserCommand::class))
            ->willReturn($dto);

        $action = new UpdateUserAction($logger, $handler);

        $request = (new ServerRequestFactory())
            ->createServerRequest('PUT', '/users/550e8400-e29b-41d4-a716-446655440000')
            ->withAttribute(
                'id',
                '550e8400-e29b-41d4-a716-446655440000',
            )
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'firstName' => 'User',
                'lastName' => 'Name',
                'isActive' => '1',
                'createdAt' => '2026-01-01 10:00:00',
            ]);

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(200, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $payload['data']['id']);

        self::assertSame('test@example.com', $payload['data']['email']);

        self::assertSame('User', $payload['data']['firstName']);

        self::assertSame('Name', $payload['data']['lastName']);

        self::assertSame(['user'], $payload['data']['roles']);

        $validator = new OpenApiValidator();
        $validator->validateResponse('/users/{id}', 'PUT', $result);
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
