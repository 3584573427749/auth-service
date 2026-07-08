<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Roles;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class GetAllRolesEndpointTest extends BaseApiTestCases {
    public function testReturnsAllRoles(): void {
        $this->loadSchema('roles');

        $this->seed('roles', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Role 1',
                'description' => 'Description for Role 1',
                'admin_level' => 1,
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
            ],
            [
                'id' => '660e8400-e29b-41d4-a716-446655440001',
                'name' => 'Role 2',
                'description' => 'Description for Role 2',
                'admin_level' => 2,
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
            ],
        ]);

        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/roles');

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
            '/roles',
            'get',
            $response,
        );
    }

    public function testReturnsEmptyArrayWhenNoRolesExist(): void {
        $this->loadSchema('roles');

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/roles');

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
            '/roles',
            'get',
            $response,
        );
    }
}
