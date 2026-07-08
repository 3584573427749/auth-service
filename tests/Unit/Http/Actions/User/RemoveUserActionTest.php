<?php

declare(strict_types=1);

namespace Http\Actions\User;

use App\Application\Handlers\User\DeleteUserHandler;
use App\Http\Actions\User\RemoveUserAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class RemoveUserActionTest extends TestCase {
    public function testReturnsVoid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(DeleteUserHandler::class);

        $handler
            ->expects($this->once())
            ->method('removeUser');

        $action = new RemoveUserAction($logger, $handler);

        $request = new ServerRequestFactory()
            ->createServerRequest('DELETE', '/users/550e8400-e29b-41d4-a716-446655440000/permanent');

        $response = new ResponseFactory()->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(204, $result->getStatusCode());

    }
}
