<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\User;

use App\Application\Commands\User\CreateUserCommand;
use App\Application\Handlers\User\CreateUserHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\User\CreateUserAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\OpenApi\OpenApiValidator;

final class CreateUserActionTest extends TestCase {
    public function testCreatesUserAndReturns201WhenRequestBodyIsValid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('test@example.com'),
            'User',
            'Name',
            true,
            new DateTimeValue('2026-06-10 10:00:00'),
            null,
        );

        $dto = UserDTO::fromUser($user);

        $handler = $this->createMock(CreateUserHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(CreateUserCommand::class))
            ->willReturn($dto);

        $action = new CreateUserAction($logger, $handler);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/users')
            ->withParsedBody([
                'email' => 'test@example.com',
                'firstName' => 'User',
                'lastName' => 'Name',
            ]);

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(201, $result->getStatusCode());

        $validator = new OpenApiValidator();
        $validator->validateResponse('/users', 'POST', $result);

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(201, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $payload['data']['id'],
        );

        self::assertSame(
            'test@example.com',
            $payload['data']['email'],
        );

        self::assertSame(
            'User',
            $payload['data']['firstName'],
        );

        self::assertSame(
            'Name',
            $payload['data']['lastName'],
        );

        self::assertSame(
            ['user'],
            $payload['data']['roles'],
        );
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
