<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\Auth;

use App\Application\Commands\Auth\RequestOneTimeCodeCommand;
use App\Application\Handlers\Auth\RequestOneTimeCodeHandler;
use App\Domain\DataTransportObjects\Auth\LoginDTO;
use App\Http\Actions\Auth\RequestOneTimeCodeAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class RequestOneTimeCodeActionTest extends TestCase {
    public function testActionCallsHandler() : void {
        $logger = $this->createMock(
            LoggerInterface::class,
        );

        $handler = $this->createMock(
            RequestOneTimeCodeHandler::class,
        );

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(
                    static function (
                        RequestOneTimeCodeCommand $command,
                    ) : bool {
                        return
                            $command->email === 'test@example.com';
                    },
                ),
            )
            ->willReturn(
                new LoginDTO(
                    'Om användaren finns har information skickats.',
                ),
            );

        $action = new RequestOneTimeCodeAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/auth/login',
            )
            ->withParsedBody([
                'email' => 'test@example.com',
            ]);

        $response = (new ResponseFactory())
            ->createResponse();

        $result = $action(
            $request,
            $response,
            [],
        );

        self::assertSame(
            200,
            $result->getStatusCode(),
        );
    }
}
