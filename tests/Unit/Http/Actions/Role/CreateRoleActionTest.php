<?php

declare(strict_types=1);

namespace Http\Actions\Role;

use App\Application\Commands\Role\CreateRoleCommand;
use App\Application\Commands\User\CreateUserCommand;
use App\Application\Handlers\Roles\CreateRoleHandler;
use App\Application\Handlers\User\CreateUserHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use App\Domain\Exception\ValidationException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\Roles\CreateRoleAction;
use App\Http\Actions\User\CreateUserAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\OpenApi\OpenApiValidator;

final class CreateRoleActionTest extends TestCase {
    public function testCreatesRoleAndReturns201WhenRequestBodyIsValid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $role = new Role(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
            'User',
            'Name',
            1,
            new DateTimeValue('2026-06-10 10:00:00'),
            null,
        );

        $dto = RoleDTO::fromRole($role);

        $handler = $this->createMock(CreateRoleHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(CreateRoleCommand::class))
            ->willReturn($dto);

        $action = new CreateRoleAction($logger, $handler);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/roles')
            ->withParsedBody([
                'name' => 'User',
                'description' => 'Name',
                'adminLevel' => 1,
            ]);

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(201, $result->getStatusCode());

        $validator = new OpenApiValidator();
        $validator->validateResponse('/roles', 'POST', $result);

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(201, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $payload['data']['id'],
        );

        self::assertSame(
            'User',
            $payload['data']['name'],
        );

        self::assertSame(
            'Name',
            $payload['data']['description'],
        );

        self::assertSame(
            1,
            $payload['data']['adminLevel'],
        );
    }

    public function testCreateRoleAndThrowsExceptionWhenRequestBodyIsInvalid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(CreateRoleHandler::class);

        $handler
            ->expects($this->never())
            ->method('handle');

        $action = new CreateRoleAction($logger, $handler);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/roles')
            ->withParsedBody([
                'name' => 'User',
                'description' => 'Name',
                'adminLevel' => -1,
            ]);

        $response = (new ResponseFactory())->createResponse();


        self::expectException(ValidationException::class);
        self::expectExceptionMessage('Felaktig indata');

        $result = $action($request, $response, []);
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
