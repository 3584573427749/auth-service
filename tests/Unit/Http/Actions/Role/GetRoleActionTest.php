<?php

declare(strict_types=1);

namespace Http\Actions\Role;

use App\Application\Handlers\Role\GetRoleHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\Entities\Role;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\RoleId;
use App\Http\Actions\Role\GetRoleAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class GetRoleActionTest extends TestCase {
    public function testReturnsRoleDTO() : void {
        $logger = $this->createMock(LoggerInterface::class);
        $role = new Role(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
            'A',
            'User',
            1,
            new DateTimeValue('2026-06-10 10:00:00'),
            null,
        );
        $roleDTO = RoleDTO::fromRole($role);

        $handler = $this->createMock(GetRoleHandler::class);

        $handler
            ->expects($this->once())
            ->method('getById')
            ->with(new RoleId('550e8400-e29b-41d4-a716-446655440000'))
            ->willReturn($roleDTO);

        $action = new GetRoleAction($logger, $handler);

        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/roles/550e8400-e29b-41d4-a716-446655440000')
            ->withAttribute('id', '550e8400-e29b-41d4-a716-446655440000');

        $response = new ResponseFactory()->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertArrayHasKey('data', $payload);

        self::assertIsArray($payload['data']);
        self::assertSame('A', $payload['data']['name']);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array<string, mixed>
     */
    private function decodeJsonResponse($response) : array {
        $body = (string)$response->getBody();

        self::assertNotSame('', $body);

        $decoded = json_decode($body, true);

        self::assertIsArray($decoded);

        return $decoded;
    }
}
