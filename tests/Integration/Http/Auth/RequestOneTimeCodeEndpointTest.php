<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Auth;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class RequestOneTimeCodeEndpointTest extends BaseApiTestCases {
    public function testRequestOneTimeCodeReturns200() : void {
        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/auth/login',
            )
            ->withParsedBody([
                'email' => 'test@example.com',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $payload = json_decode((string)$response->getBody(), true);

        self::assertIsArray($payload);

        self::assertArrayHasKey('data', $payload);

        self::assertSame('Om användaren finns har information skickats.', $payload['data']['message']);

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/auth/login',
            'post',
            $response,
        );
    }

    public function testRequestOneTimeCodeWithUnknownUserReturns200() : void {
        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/auth/login',
            )
            ->withParsedBody(['email' => 'missing@example.com', ]);

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $payload = json_decode((string)$response->getBody(), true);

        self::assertSame('Om användaren finns har information skickats.', $payload['data']['message']);

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/auth/login',
            'post',
            $response,
        );
    }

    protected function setUp() : void {
        parent::setUp();
        $this->loadSchema('users');
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
                'code_hash' => sha1('123456'),
                'expires_at' => '2026-06-10 11:00:00',
                'created_at' => '2026-06-10 10:00:00',
            ],
        ]);

    }
}
