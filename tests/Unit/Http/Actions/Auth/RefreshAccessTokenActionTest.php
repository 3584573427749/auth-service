<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\Auth;

use App\Application\Commands\Auth\RefreshAccessTokenCommand;
use App\Application\Handlers\Auth\RefreshAccessTokenHandler;
use App\Domain\DataTransportObjects\Auth\AuthenticationDTO;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\Auth\RefreshAccessTokenAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class RefreshAccessTokenActionTest extends TestCase {
    public function testActionReturnsAuthenticationResponse() : void {
        $logger = $this->createMock(
            LoggerInterface::class,
        );

        $handler = $this->createMock(
            RefreshAccessTokenHandler::class,
        );
        $userDTO = UserDTO::fromUser(
            new User(
                new UserId('550e8400-e29b-41d4-a716-446655440000'),
                new Email('test@example.com'),
                'User',
                'Name',
                new DateTimeValue('2026-01-01T00:00:00+00:00'),
                null,
                null,
            ),
        )->withRoles([]);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(
                    static fn (
                        RefreshAccessTokenCommand $command,
                    ) : bool => $command->refreshToken === 'refresh-token',
                ),
            )
            ->willReturn(
                new AuthenticationDTO(
                    'access-token',
                    'refresh-token',
                    $userDTO,
                ),
            );

        $action = new RefreshAccessTokenAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/auth/refresh',
            )
            ->withParsedBody([
                'refreshToken' => 'refresh-token',
            ]);

        $response = (new ResponseFactory())
            ->createResponse();

        $result = $action(
            $request,
            $response,
            [],
        );

        self::assertInstanceOf(
            ResponseInterface::class,
            $result,
        );

        self::assertSame(
            200,
            $result->getStatusCode(),
        );

        $payload = json_decode(
            (string)$result->getBody(),
            true,
        );

        self::assertIsArray(
            $payload,
        );

        self::assertSame(
            'access-token',
            $payload['data']['accessToken'],
        );

        self::assertSame(
            'refresh-token',
            $payload['data']['refreshToken'],
        );

        self::assertSame(
            'test@example.com',
            $payload['data']['user']['email'],
        );
    }
}
