<?php

declare(strict_types=1);

namespace Http\Actions\Role;

use App\Application\Handlers\Roles\DeleteRoleHandler;
use App\Application\Handlers\User\DeleteUserHandler;
use App\Http\Actions\Roles\DeleteRoleAction;
use App\Http\Actions\User\DeleteUserAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class DeleteRoleActionTest extends TestCase {
    public function testReturnsVoid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(DeleteRoleHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle');

        $action = new DeleteRoleAction($logger, $handler);

        $request = new ServerRequestFactory()
            ->createServerRequest('DELETE', '/roles/550e8400-e29b-41d4-a716-446655440000');

        $response = new ResponseFactory()->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(204, $result->getStatusCode());

    }
}
