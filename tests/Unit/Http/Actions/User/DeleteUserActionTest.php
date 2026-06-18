<?php

declare(strict_types=1);

namespace Http\Actions\User;

use App\Application\Handlers\User\DeleteUserHandler;
use App\Http\Actions\User\DeleteUserAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\OpenApi\OpenApiValidator;

final class DeleteUserActionTest extends TestCase {
    public function testReturnsVoid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(DeleteUserHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle');

        $action = new DeleteUserAction($logger, $handler);

        $request = new ServerRequestFactory()
            ->createServerRequest('DELETE', '/users/550e8400-e29b-41d4-a716-446655440000');

        $response = new ResponseFactory()->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(204, $result->getStatusCode());

        $validator = new OpenApiValidator();
        $validator->validateResponse('/users/{id}', 'DELETE', $result);
    }
}
