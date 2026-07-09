<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\UserRole;

use App\Application\Commands\UserRole\UserRoleCommand;
use App\Application\Handlers\UserRole\DeleteUserRoleHandler;
use App\Http\Actions\UserRole\DeleteUserRoleAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class DeleteUserRoleActionTest extends TestCase {
    public function testDeletesUserRoleAndReturns204() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(DeleteUserRoleHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(
                    static function (UserRoleCommand $command) : bool {
                        return
                            $command->userId === '550e8400-e29b-41d4-a716-446655440000'
                            &&
                            $command->roleId === '660e8400-e29b-41d4-a716-446655440001';
                    },
                ),
            );

        $action = new DeleteUserRoleAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest('DELETE', '/users/550e8400-e29b-41d4-a716-446655440000/roles/660e8400-e29b-41d4-a716-446655440001')
            ->withAttribute('id', '550e8400-e29b-41d4-a716-446655440000')
            ->withAttribute('roleId', '660e8400-e29b-41d4-a716-446655440001');

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(204, $result->getStatusCode());
    }
}
