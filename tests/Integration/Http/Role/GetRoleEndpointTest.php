<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Role;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class GetRoleEndpointTest extends BaseApiTestCases {
    public function testGetRoleReturns200(): void {
        $this->loadSchema('roles');

        $this->seed('roles', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Test Role',
                'description' => 'A test role',
                'admin_level' => 1,
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/roles/550e8400-e29b-41d4-a716-446655440000',
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
            '/roles/{id}',
            'get',
            $response,
        );
    }

    public function testGetRoleReturns400WhenIdIsNotAUuid(): void {
        $this->loadSchema('roles');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/roles/invalid-id',
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
            '/roles/{id}',
            'get',
            $response,
        );
    }

    public function testGetRoleReturns404WhenRoleDoesNotExist(): void {
        $this->loadSchema('roles');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/roles/550e8400-e29b-41d4-a716-446655440000',
            );

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/roles/{id}',
            'get',
            $response,
        );
    }
}
