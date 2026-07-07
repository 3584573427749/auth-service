<?php

declare(strict_types=1);

namespace Http\Actions\Role;

use App\Application\Commands\Role\UpdateRoleCommand;
use App\Application\Handlers\Roles\UpdateRoleHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\Entities\Role;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\RoleId;
use App\Http\Actions\Roles\UpdateRoleAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class UpdateRoleActionTest extends TestCase {
    public function testUpdatesRoleAndReturns200WhenRequestBodyIsValid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $role = new Role(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
            'User',
            'Name',
            1,
            new DateTimeValue('2026-06-10T10:00:00+00:00'),
            new DateTimeValue('2026-06-11T10:00:00+00:00'),
        );

        $dto = RoleDTO::fromRole($role);

        $handler = $this->createMock(UpdateRoleHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(UpdateRoleCommand::class))
            ->willReturn($dto);

        $action = new UpdateRoleAction($logger, $handler);

        $request = (new ServerRequestFactory())
            ->createServerRequest('PUT', '/roles/550e8400-e29b-41d4-a716-446655440000')
            ->withAttribute(
                'id',
                '550e8400-e29b-41d4-a716-446655440000',
            )
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'User',
                'description' => 'Name',
                'adminLevel' => 1,
                'createdAt' => '2026-01-01T10:00:00+00:00',
                'updatedAt' => null,
            ]);

        $response = new ResponseFactory()->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(200, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $payload['data']['id']);

        self::assertSame('User', $payload['data']['name']);

        self::assertSame('Name', $payload['data']['description']);

        self::assertSame(1, $payload['data']['adminLevel']);
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
