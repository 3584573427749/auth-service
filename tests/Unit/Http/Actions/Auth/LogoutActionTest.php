<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\Auth;

use App\Http\Actions\Auth\LogoutAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class LogoutActionTest extends TestCase {
    public function testLogoutReturnsResponseAndExpiresRefreshTokenCookie() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $action = new LogoutAction($logger, );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/auth/refresh',
            );

        $response = (new ResponseFactory())
            ->createResponse();

        $result = $action(
            $request,
            $response,
            [],
        );

        self::assertInstanceOf(ResponseInterface::class, $result);

        self::assertSame(200, $result->getStatusCode());

        $payload = json_decode((string)$result->getBody(), true);

        self::assertIsArray($payload);

        self::assertSame('Utloggad', $payload['data']['message']);

        $cookie = $result->getHeaderLine('Set-Cookie');

        self::assertStringContainsString('refreshToken=', $cookie);

        self::assertStringContainsString('Max-Age=0', $cookie);

        self::assertStringContainsString('Path=/auth/refresh', $cookie);

        self::assertStringContainsString('HttpOnly', $cookie);

        self::assertStringContainsString('Secure', $cookie);

        self::assertStringContainsString('SameSite=Strict', $cookie);
    }
}
