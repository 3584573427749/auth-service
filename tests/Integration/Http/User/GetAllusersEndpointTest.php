<?php

declare(strict_types=1);

namespace Tests\Integration\Http\User;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class GetAllUsersEndpointTest extends BaseApiTestCases {
    public function testReturnsAllUsers() : void {
        $this->loadSchema('users');
        $this->loadSchema('roles');
        $this->loadSchema('user_roles');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'user1@example.com',
                'first_name' => 'User',
                'last_name' => 'One',
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => '660e8400-e29b-41d4-a716-446655440001',
                'email' => 'user2@example.com',
                'first_name' => 'User',
                'last_name' => 'Two',
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);

        $this->seed('roles', [
            [
                'id' => '11111111-1111-1111-1111-111111111111',
                'name' => 'User',
                'description' => 'Regular user',
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
            ],
        ]);

        $this->seed('user_roles', [
            [
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'role_id' => '11111111-1111-1111-1111-111111111111',
            ],
            [
                'user_id' => '660e8400-e29b-41d4-a716-446655440001',
                'role_id' => '11111111-1111-1111-1111-111111111111',
            ],
        ]);

        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/users');

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $payload = json_decode(
            (string)$response->getBody(),
            true,
        );

        self::assertIsArray($payload);

        self::assertSame(200, $payload['statusCode']);
        self::assertCount(2, $payload['data']);

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users',
            'get',
            $response,
        );
    }

    public function testReturnsEmptyArrayWhenNoUsersExist() : void {
        $this->loadSchema('users');

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/users');

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $payload = json_decode(
            (string)$response->getBody(),
            true,
        );

        self::assertIsArray($payload);

        self::assertSame([], $payload['data']);

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users',
            'get',
            $response,
        );
    }
}
