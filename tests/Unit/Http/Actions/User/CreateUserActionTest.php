<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\User;

use App\Application\Actions\User\CreateUserAction;
use App\Application\Commands\User\CreateUserCommand;
use App\Application\Handlers\User\CreateUserHandler;
use App\Domain\DataTransportObjects\CreateUserDTO;
use App\Domain\Entities\User;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class CreateUserActionTest extends TestCase {
    public function testReturns422WhenRequestBodyIsInvalid(): void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(CreateUserHandler::class);
        $handler
            ->expects($this->never())
            ->method('handle');

        $test = new \App\Http\Actions\User\CreateUserAction($logger, $handler);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/users')
            ->withParsedBody([
                'email' => 'invalid-email',
                'first_name' => '',
                'last_name' => '',
            ]);

        $response = (new ResponseFactory())->createResponse();

        $result = $test->action($request, $response, []);

        self::assertSame(422, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(422, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);
        self::assertIsArray($payload['data']);

        self::assertArrayHasKey('error', $payload['data']);
        self::assertSame('Validation failed.', $payload['data']['error']);

        self::assertArrayHasKey('fields', $payload['data']);
        self::assertIsArray($payload['data']['fields']);

        self::assertArrayHasKey('indata', $payload['data']);
        self::assertIsArray($payload['data']['indata']);
    }

    public function testCreatesUserAndReturns201WhenRequestBodyIsValid(): void {
        $logger = $this->createMock(LoggerInterface::class);

        $expectedDto = CreateUserDTO::fromUser(User::fromDBRow([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'first_name' => 'User',
            'last_name' => 'Name',
            'is_active' => true,
            'created_at' => '2026-06-10 10:00:00',
            'updated_at' => null,])
        );

        $handler = $this->createMock(CreateUserHandler::class);
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(CreateUserCommand::class))
            ->willReturn($expectedDto);

        $action = new \App\Http\Actions\User\CreateUserAction($logger, $handler);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/users')
            ->withParsedBody([
                'email' => 'test@example.com',
                'first_name' => 'User',
                'last_name' => 'Name',
            ]);

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(201, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(201, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);
        self::assertIsArray($payload['data']);

        self::assertSame($expectedDto['id'], $payload['data']['id']);
        self::assertSame($expectedDto['email'], $payload['data']['email']);
        self::assertSame($expectedDto['first_name'], $payload['data']['first_name']);
        self::assertSame($expectedDto['last_name'], $payload['data']['last_name']);
        self::assertSame($expectedDto['is_active'], $payload['data']['is_active']);

        self::assertArrayHasKey('roles', $payload['data']);
        self::assertContains('user', $payload['data']['roles']);

        self::assertSame($expectedDto['created_at'], $payload['data']['created_at']);
        self::assertNull($payload['data']['updated_at']);
    }

    private function decodeJsonResponse(ResponseInterface $response): array {
        $body = (string)$response->getBody();

        self::assertNotSame('', $body, 'Response body should not be empty.');

        $decoded = json_decode($body, true);

        self::assertIsArray(
            $decoded,
            sprintf('Response body is not valid JSON: %s', $body)
        );

        return $decoded;
    }
}