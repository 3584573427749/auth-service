<?php

declare(strict_types=1);

namespace Tests\Integration\Http\UserRole;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class CreateUserRoleEndpointTest extends BaseApiTestCases {
    public function testReturns204WhenRequestIsValid(): void {
        $requestBody = [
            'roleId' => '22222222-2222-2222-2222-222222222222',
        ];

        $validator = new OpenApiValidator();

        $request = new ServerRequestFactory()
            ->createServerRequest('POST', '/users/11111111-1111-1111-1111-111111111111/roles')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(
            json_encode($requestBody, JSON_THROW_ON_ERROR),
        );

        $request = $request->withParsedBody($requestBody);

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        self::assertSame(204, $response->getStatusCode());

        $validator->validateResponse(
            '/users/{userId}/roles',
            'post',
            $response,
        );
    }

    public function testReturns404WhenUserDoesNotExist(): void {
        $requestBody = [
            'roleId' => '22222222-2222-2222-2222-222222222222',
        ];

        $request = new ServerRequestFactory()
            ->createServerRequest('POST', '/users/11111111-1111-1111-1111-111111111112/roles')
            ->withParsedBody($requestBody);

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}/roles',
            'post',
            $response,
        );

    }

    public function testReturns400WithInvalidId(): void {
        $requestBody = [
            'roleId' => '22222222-2222-2222-2222-222222222222',
        ];

        $request = new ServerRequestFactory()
            ->createServerRequest('POST', '/users/11111111/roles')
            ->withParsedBody($requestBody);

        $response = $this->app->handle($request);

        self::assertSame(400, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{userId}/roles',
            'post',
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
    }
}
