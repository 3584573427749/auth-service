<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Actions\Auth;

use App\Application\Services\JwtService;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Services\FirebaseJwtService;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;

final class RefreshAccessTokenActionTest extends BaseApiTestCases {
    protected function setUp() : void {
        parent::setUp();

        $this->loadSchema('users');
        $this->loadSchema('roles');
        $this->loadSchema('user_roles');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'first_name' => 'User',
                'last_name' => 'Name',
                'created_at' => '2026-01-01 00:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);
    }

    public function testRefreshReturnsNewTokens() : void {
        /** @var JwtService $jwtService */
        $jwtService = $this->app->getContainer()->get(JwtService::class);

        $refreshToken = $jwtService->createRefreshToken($this->createUser());

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/auth/refresh',
            )
            ->withParsedBody(['refreshToken' => $refreshToken, ]);

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $payload = json_decode((string)$response->getBody(), true);

        self::assertIsArray($payload);

        self::assertArrayHasKey('data', $payload);

        self::assertArrayHasKey('accessToken', $payload['data']);

        self::assertArrayHasKey('refreshToken', $payload['data']);

        self::assertArrayHasKey('user', $payload['data']);
    }

    public function testRefreshFailsWhenAccessTokenIsUsed() : void {
        /** @var JwtService $jwtService */
        $jwtService = $this->app->getContainer()->get(JwtService::class);

        $accessToken = $jwtService->createAccessToken($this->createUser(), []);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/auth/refresh',
            )
            ->withParsedBody([
                'refreshToken' => $accessToken,
            ]);

        $response = $this->app->handle($request);

        self::assertSame(422, $response->getStatusCode());
    }

    public function testRefreshFailsForExpiredRefreshToken() : void {
        $jwtService = new FirebaseJwtService(
            $_ENV['JWT_SECRET'],
            $_ENV['JWT_ISSUER'],
            (int)$_ENV['JWT_ACCESS_TOKEN_LIFETIME'],
            -60,
        );

        $refreshToken = $jwtService->createRefreshToken($this->createUser());

        $request = (new ServerRequestFactory())->createServerRequest(
            'POST',
            '/auth/refresh',
        )
            ->withParsedBody(['refreshToken' => $refreshToken, ]);

        $response = $this->app->handle($request);

        self::assertSame(422, $response->getStatusCode());
    }

    public function testRefreshFailsWhenUserDoesNotExist() : void {
        /** @var JwtService $jwtService */
        $jwtService = $this->app->getContainer()->get(JwtService::class);

        $missingUser = new User(
            new UserId('99999999-9999-9999-9999-999999999999', ),
            new Email('missing@example.com', ),
            'Missing',
            'User',
            new DateTimeValue('2026-01-01 00:00:00', ),
            null,
            null,
        );

        $refreshToken = $jwtService->createRefreshToken($missingUser);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/auth/refresh',
            )
            ->withParsedBody(['refreshToken' => $refreshToken, ]);

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());
    }

    private function createUser() : User {
        return new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            new Email('test@example.com', ),
            'User',
            'Name',
            new DateTimeValue('2026-01-01 00:00:00', ),
            null,
            null,
        );
    }
}
