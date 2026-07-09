<?php

declare(strict_types=1);

namespace Tests\Integration\Http\UserRole;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class GetRoleUsersEndpointTest extends BaseApiTestCases {
    public function testReturns200WhenRequestIsValid(): void {
        $validator = new OpenApiValidator();

        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/roles/22222222-2222-2222-2222-222222222222/users');

        $request = $request->withParsedBody($requestBody);

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        $content=(json_decode((string)$response->getBody(), true));
        self::assertSame(200, $response->getStatusCode());
        self::assertCount(1, $content['data']);

        $validator->validateResponse(
            '/roles/{id}/users',
            'get',
            $response,
        );
    }

    public function testReturns404WhenRoleDoesNotExist(): void {
        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/roles/33333333-3333-3333-3333-333333333333/users');

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/roles/{id}/users',
            'get',
            $response,
        );

    }

    public function testReturns400WithInvalidId(): void {
        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/roles/11111111/users');

        $response = $this->app->handle($request);

        self::assertSame(400, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/roles/{id}/users',
            'get',
            $response,
        );
    }

    protected function setUp(): void {
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
                'user_id' => '11111111-1111-1111-1111-222222222222',
                'role_id' => '33333333-3333-3333-3333-333333333333',
            ],
        ]);
    }
}
