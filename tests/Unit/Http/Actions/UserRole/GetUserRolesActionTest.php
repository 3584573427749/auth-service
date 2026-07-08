<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\UserRole;

use App\Application\Handlers\UserRole\GetUserRolesHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\UserRole\GetUserRolesAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class GetUserRolesActionTest extends TestCase {
    public function testReturnsRolesAnd200WhenUserExists() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $roleDto = $this->createMock(RoleDTO::class);

        $handler = $this->createMock(GetUserRolesHandler::class);

        $handler
            ->expects($this->once())
            ->method('getRoles')
            ->with(
                $this->callback(
                    static function (UserId $userId) : bool {
                        return $userId->toString()
                            === '550e8400-e29b-41d4-a716-446655440000';
                    },
                ),
            )
            ->willReturn([$roleDto]);

        $action = new GetUserRolesAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/users/550e8400-e29b-41d4-a716-446655440000/roles',
            )
            ->withAttribute(
                'id',
                '550e8400-e29b-41d4-a716-446655440000',
            );

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(200, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);

        self::assertCount(1, $payload['data']);
    }

    public function testReturnsEmptyArrayWhenUserHasNoRoles() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(GetUserRolesHandler::class);

        $handler
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn([]);

        $action = new GetUserRolesAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/users/550e8400-e29b-41d4-a716-446655440000/roles',
            )
            ->withAttribute(
                'id',
                '550e8400-e29b-41d4-a716-446655440000',
            );

        $response = (new ResponseFactory())->createResponse();

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
