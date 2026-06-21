<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Users;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class GetUserEndpointTest extends BaseApiTestCases {
    public function testGetUserReturns200() : void {
        $this->loadSchema('users');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'first_name' => 'User',
                'last_name' => 'Name',
                'is_active' => 1,
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/users/550e8400-e29b-41d4-a716-446655440000',
            );

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $payload = json_decode(
            (string)$response->getBody(),
            true,
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $payload['data']['id'],
        );

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}',
            'get',
            $response,
        );
    }

    public function testGetUserReturns400WhenIdIsNotAUuid() : void {
        $this->loadSchema('users');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/users/invalid-id',
            );

        $response = $this->app->handle($request);

        self::assertSame(400, $response->getStatusCode());

        $payload = json_decode(
            (string)$response->getBody(),
            true,
        );

        self::assertIsArray($payload);

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}',
            'get',
            $response,
        );
    }

    public function testGetUserReturns404WhenUserDoesNotExist() : void {
        $this->loadSchema('users');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/users/550e8400-e29b-41d4-a716-446655440000',
            );

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}',
            'get',
            $response,
        );
    }
}
