<?php

declare(strict_types=1);

namespace Tests\Integration\Http\UserRole;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class GetUserRolesEndpointTest extends BaseApiTestCases {
    public function testReturns200WhenRequestIsValid() : void {
        $validator = new OpenApiValidator();

        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/users/11111111-1111-1111-1111-111111111111/roles');

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        $content = (json_decode((string)$response->getBody(), true));
        self::assertSame(200, $response->getStatusCode());
        self::assertCount(2, $content['data']);

        $validator->validateResponse(
            '/users/{userId}/roles',
            'get',
            $response,
        );
    }

    public function testReturns404WhenUserDoesNotExist() : void {
        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/users/11111111-1111-1111-1111-111111111113/roles');

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}/roles',
            'get',
            $response,
        );

    }

    public function testReturns400WithInvalidId() : void {
        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/users/11111111/roles');

        $response = $this->app->handle($request);

        self::assertSame(400, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{userId}/roles',
            'get',
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
                'id' => '33333333-3333-3333-3333-333333333333',
                'name' => 'Another Test Role',
                'description' => 'Another test role',
                'admin_level' => 2,
            ],
        ]);
        $this->seed('user_roles', [
            [
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'role_id' => '22222222-2222-2222-2222-222222222222',
            ],
            [
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'role_id' => '33333333-3333-3333-3333-333333333333',
            ],
            [
                'user_id' => '11111111-1111-1111-1111-111111111112',
                'role_id' => '33333333-3333-3333-3333-333333333333',
            ],
        ]);
    }
}
