<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\UserRole;

use App\Application\Handlers\UserRole\GetUserRolesHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\ValueObjects\RoleId;
use App\Http\Actions\UserRole\GetRoleUsersAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class GetRoleUsersActionTest extends TestCase {
    public function testReturnsUsersAnd200WhenRoleExists() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $userDto = $this->createMock(UserDTO::class);

        $handler = $this->createMock(GetUserRolesHandler::class);

        $handler
            ->expects($this->once())
            ->method('getUsers')
            ->with(
                $this->callback(
                    static function (RoleId $roleId) : bool {
                        return $roleId->toString()
                            === '550e8400-e29b-41d4-a716-446655440000';
                    },
                ),
            )
            ->willReturn([$userDto]);

        $action = new GetRoleUsersAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/roles/550e8400-e29b-41d4-a716-446655440000/users')
            ->withAttribute('id', '550e8400-e29b-41d4-a716-446655440000');

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(200, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);

        self::assertCount(1, $payload['data']);
    }

    public function testReturnsEmptyArrayWhenRoleHasNoUsers() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(GetUserRolesHandler::class);

        $handler
            ->expects($this->once())
            ->method('getUsers')
            ->willReturn([]);

        $action = new GetRoleUsersAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/roles/550e8400-e29b-41d4-a716-446655440000/users')
            ->withAttribute('id', '550e8400-e29b-41d4-a716-446655440000');

        $response = new ResponseFactory()->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame([], $payload['data']);
    }

    /**
     * @return array<string,mixed>
     */
    private function decodeJsonResponse(ResponseInterface $response) : array {
        $body = (string)$response->getBody();

        self::assertNotSame('', $body);

        $decoded = json_decode($body, true);

        self::assertIsArray($decoded);

        return $decoded;
    }
}
