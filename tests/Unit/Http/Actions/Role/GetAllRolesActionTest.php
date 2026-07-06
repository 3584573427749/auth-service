<?php

declare(strict_types=1);

namespace Http\Actions\Role;

use App\Application\Handlers\Roles\GetRoleHandler;
use App\Application\Handlers\User\GetUserHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\Roles\GetAllRolesAction;
use App\Http\Actions\User\GetAllUsersAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\OpenApi\OpenApiValidator;

final class GetAllRolesActionTest extends TestCase {
    public function testReturnsAllRoles() : void {
        $logger = $this->createMock(LoggerInterface::class);
        $roles = [];
        $role = new Role(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
            'A',
            'User',
            1,
            new DateTimeValue('2026-06-10 10:00:00'),
            null,
        );
        $roles[] = RoleDTO::fromRole($role);

        $role = new Role(
            new RoleId('660e8400-e29b-41d4-a716-446655440000'),
            'B',
            'User',
            2,
            new DateTimeValue('2026-06-10 10:00:00'),
            null,
        );
        $roles[] = RoleDTO::fromRole($role);

        $handler = $this->createMock(GetRoleHandler::class);

        $handler
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($roles);

        $action = new GetAllRolesAction($logger, $handler);

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/roles');

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $validator = new OpenApiValidator();
        $validator->validateResponse('/roles', 'GET', $result);

        $payload = $this->decodeJsonResponse($result);

        self::assertArrayHasKey('data', $payload);

        self::assertIsArray($payload['data']);

        self::assertCount(2, $payload['data']);

        self::assertSame('A', $payload['data'][0]['name']);
        self::assertSame('B', $payload['data'][1]['name']);
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
