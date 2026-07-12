<?php

declare(strict_types=1);

namespace Tests\Integration\Http\UserRole;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class DeleteUserRolesEndpointTest extends BaseApiTestCases {
    public function testReturns204WhenRequestIsValid() : void {
        $validator = new OpenApiValidator();

        $request = new ServerRequestFactory()
            ->createServerRequest('DELETE', '/users/11111111-1111-1111-1111-111111111111/roles/22222222-2222-2222-2222-222222222222');

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        $content = (json_decode((string)$response->getBody(), true));
        self::assertSame(204, $response->getStatusCode());

        $validator->validateResponse(
            '/users/{userId}/roles/{roleId}',
            'delete',
            $response,
        );
    }

    public function testReturns404WhenUserDoesNotExist() : void {
        $request = new ServerRequestFactory()
            ->createServerRequest('DELETE', '/users/11111111-1111-1111-1111-111111111113/roles/22222222-2222-2222-2222-222222222222');

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}/roles/{roleId}',
            'delete',
            $response,
        );

    }

    public function testReturns404WhenRoleDoesNotExist() : void {
        $request = new ServerRequestFactory()
            ->createServerRequest('DELETE', '/users/11111111-1111-1111-1111-111111111111/roles/33333333-3333-3333-3333-333333333333');

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}/roles/{roleId}',
            'delete',
            $response,
        );

    }

    public function testReturns404WhenRecordDoesNotExist() : void {
        $request = new ServerRequestFactory()
            ->createServerRequest('DELETE', '/users/11111111-1111-1111-1111-111111111114/roles/44444444-4444-4444-4444-444444444444');

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}/roles/{roleId}',
            'delete',
            $response,
        );

    }

    public function testReturns400WithInvalidId() : void {
        $request = new ServerRequestFactory()
            ->createServerRequest('DELETE', '/users/11111111/roles/22222222-2222-2222-2222-222222222222');

        $response = $this->app->handle($request);

        self::assertSame(400, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}/roles/{roleId}',
            'delete',
            $response,
        );
    }

    protected function setUp() : void {
        parent::setUp();

        $this->loadSchema('users');
        $this->loadSchema('roles');
        $this->loadSchema('user_roles');

        $this->seed('users', [
            [
                'id' => '11111111-1111-1111-1111-111111111111',
                'email' => 'test@example.com',
                'first_name' => 'Test',
                'last_name' => 'User',
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);
        $this->seed('roles', [
            [
                'id' => '22222222-2222-2222-2222-222222222222',
                'name' => 'Test Role',
                'description' => 'A test role',
                'admin_level' => 1,
            ],
            [
                'id' => '44444444-4444-4444-4444-444444444444',
                'name' => 'Another test role',
                'description' => 'A test role',
                'admin_level' => 1,
            ],
        ]);
        $this->seed('user_roles', [
            [
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'role_id' => '22222222-2222-2222-2222-222222222222',
            ],
            [
                'user_id' => '11111111-1111-1111-1111-111111111112',
                'role_id' => '33333333-3333-3333-3333-333333333333',
            ],
        ]);
    }
}
