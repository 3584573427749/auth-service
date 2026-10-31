<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Auth;

use Safe\DateTimeImmutable;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class VerifyOneTimeCodeEndpointTest extends BaseApiTestCases {
    protected function setUp() : void {
        parent::setUp();
        $this->loadSchema('users');
        $this->loadSchema('roles');
        $this->loadSchema('user_roles');
        $this->loadSchema('one_time_codes');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'first_name' => 'User',
                'last_name' => 'Name',
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);

        $this->seed('one_time_codes', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'code_hash' => '123456', // hash('sha256', '123456'),
                'expires_at' => new DateTimeImmutable('+10 minutes')->format('Y-m-d H:i:s'),
                'created_at' => new DateTimeImmutable()->format('Y-m-d H:i:s'),
            ],
        ]);

    }

    public function testVerifyOneTimeCodeReturns200() : void {
        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/auth/verify',
            )
            ->withParsedBody([
                'email' => 'test@example.com',
                'code' => '123456',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(
            200,
            $response->getStatusCode(),
        );

        $payload = json_decode(
            (string) $response->getBody(),
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

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/auth/verify',
            'post',
            $response,
        );
    }

    public function testVerifyOneTimeCodeReturns422ForInvalidCode() : void {
        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/auth/verify',
            )
            ->withParsedBody([
                'email' => 'test@example.com',
                'code' => '999999',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(
            422,
            $response->getStatusCode(),
        );

        $payload = json_decode(
            (string) $response->getBody(),
            true,
        );

        self::assertIsArray($payload);

        self::assertArrayHasKey(
            'error',
            $payload,
        );

        self::assertSame(
            'ValidationException',
            $payload['error']['type'],
        );
    }
}
