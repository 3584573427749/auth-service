<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Role;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class UpdateRoleEndpointTest extends BaseApiTestCases {
    public function testReturns200WhenRequestIsValid() : void {
        $this->loadSchema('roles');

        $this->seed('roles', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Old role',
                'description' => 'Old Description',
                'admin_level' => 1,
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
            ],
        ]);

        $requestBody = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'New',
            'description' => 'Name',
            'adminLevel' => 1,
            'createdAt' => '2026-06-10T10:00:00+00:00',
            'updatedAt' => null,
        ];

        $validator = new OpenApiValidator();

        $request = (new ServerRequestFactory())
            ->createServerRequest('PUT', '/roles/550e8400-e29b-41d4-a716-446655440000')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode($requestBody, JSON_THROW_ON_ERROR));

        $request = $request->withParsedBody($requestBody);

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        (new OpenApiValidator())->validateResponse(
            '/roles/{id}',
            'put',
            $response,
        );
    }

    public function testReturns404WhenRoleIsNotFound() : void {
        $this->loadSchema('roles');

        $this->seed('roles', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Old role',
                'description' => 'Old Description',
                'admin_level' => 1,
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/roles/550e8400-e29b-41d4-a716-446655440001',
            )
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'name' => 'New role',
                'description' => 'New Description',
                'adminLevel' => 1,
                'createdAt' => '2026-06-10 10:00:00',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());

        (new OpenApiValidator())->validateResponse(
            '/roles/{id}',
            'put',
            $response,
        );
    }

    public function testReturns409WhenNameAlreadyExists() : void {
        $this->loadSchema('roles');

        $this->seed('roles', [
            [
                'id' => '11111111-1111-1111-1111-111111111111',
                'name' => 'Existing role',
                'description' => 'Existing Description',
                'admin_level' => 1,
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
            ],
            [
                'id' => '22222222-2222-2222-2222-222222222222',
                'name' => 'Another role',
                'description' => 'Another Description',
                'admin_level' => 1,
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/roles/22222222-2222-2222-2222-222222222222',
            )
            ->withParsedBody([
                'id' => '22222222-2222-2222-2222-222222222222',
                'name' => 'Existing role',
                'description' => 'Another Description',
                'adminLevel' => 1,
                'createdAt' => '2026-06-10 10:00:00',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(409, $response->getStatusCode());

        (new OpenApiValidator())->validateResponse(
            '/roles/{id}',
            'put',
            $response,
        );
    }

    public function testReturns422WhenValidationFails() : void {
        $this->loadSchema('roles');

        $this->seed('roles', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'User',
                'description' => 'User Description',
                'admin_level' => 1,
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/roles/550e8400-e29b-41d4-a716-446655440000',
            )
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Invalid Role',
                'description' => '',
                'adminLevel' => -1,
                'createdAt' => '2026-06-10 10:00:00',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(422, $response->getStatusCode());

        (new OpenApiValidator())->validateResponse(
            '/roles/{id}',
            'put',
            $response,
        );
    }
}
