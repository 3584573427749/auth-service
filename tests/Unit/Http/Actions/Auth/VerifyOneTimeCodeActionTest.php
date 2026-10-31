<?php


declare(strict_types=1);

namespace Tests\Unit\Http\Actions\Auth;

use App\Application\Commands\Auth\VerifyOneTimeCodeCommand;
use App\Application\Handlers\Auth\VerifyOneTimeCodeHandler;
use App\Domain\DataTransportObjects\Auth\AuthenticationDTO;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\Auth\VerifyOneTimeCodeAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class VerifyOneTimeCodeActionTest extends TestCase {
    private function createUser() : User {
        return new User(
            new UserId(
                '550e8400-e29b-41d4-a716-446655440000',
            ),
            new Email(
                'test@example.com',
            ),
            'User',
            'Name',
            new DateTimeValue('2026-01-01 00:00:00'),
            null,
            null,
        );
    }

    public function testReturnsVerifyOneTimeCodeDto() : void {
        $logger = $this->createMock(
            LoggerInterface::class,
        );

        $otpDto = new AuthenticationDTO(
            'access-token',
            'refresh-token',
            UserDTO::fromUser($this->createUser()),
        );

        $handler = $this->createMock(
            VerifyOneTimeCodeHandler::class,
        );

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->isInstanceOf(
                    VerifyOneTimeCodeCommand::class,
                ),
            )
            ->willReturn($otpDto);

        $action = new VerifyOneTimeCodeAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/auth/verify',
            )
            ->withParsedBody([
                'email' => 'test@example.com',
                'code' => '123456',
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

        $payload = json_decode(
            (string)$result->getBody(),
            true,
        );

        self::assertIsArray($payload);

        self::assertArrayHasKey(
            'data',
            $payload,
        );

        self::assertArrayHasKey(
            'user',
            $payload['data'],
        );

        self::assertArrayHasKey(
            'email',
            $payload['data']['user'],
        );

        self::assertSame(
            'test@example.com',
            $payload['data']['user']['email'],
        );
    }
}
