<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Users;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class UpdateUserEndpointTest extends BaseApiTestCases {
    public function testReturns200WhenRequestIsValid() : void {
        $this->loadSchema('users');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'old@example.com',
                'first_name' => 'Old',
                'last_name' => 'Name',
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);

        $requestBody = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'old@example.com',
            'firstName' => 'New',
            'lastName' => 'Name',
            'createdAt' => '2026-06-10T10:00:00+00:00',
            'updatedAt' => null,
            'deletedAt' => null,
        ];

        $validator = new OpenApiValidator();

        $request = (new ServerRequestFactory())
            ->createServerRequest('PUT', '/users/550e8400-e29b-41d4-a716-446655440000')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode($requestBody, JSON_THROW_ON_ERROR));

        $request = $request->withParsedBody($requestBody);

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        (new OpenApiValidator())->validateResponse(
            '/users/{id}',
            'put',
            $response,
        );
    }

    public function testReturns404WhenUserIsNotFound() : void {
        $this->loadSchema('users');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'old@example.com',
                'first_name' => 'Old',
                'last_name' => 'Name',
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/users/550e8400-e29b-41d4-a716-446655440001',
            )
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'email' => 'noone@example.com',
                'firstName' => 'New',
                'lastName' => 'Name',
                'createdAt' => '2026-06-10 10:00:00',
                'updatedAt' => null,
                'deletedAt' => null,
            ]);

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());

        (new OpenApiValidator())->validateResponse(
            '/users/{id}',
            'put',
            $response,
        );
    }

    public function testReturns409WhenEmailAlreadyExists() : void {
        $this->loadSchema('users');

        $this->seed('users', [
            [
                'id' => '11111111-1111-1111-1111-111111111111',
                'email' => 'existing@example.com',
                'first_name' => 'Existing',
                'last_name' => 'User',
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => '22222222-2222-2222-2222-222222222222',
                'email' => 'other@example.com',
                'first_name' => 'Other',
                'last_name' => 'User',
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/users/22222222-2222-2222-2222-222222222222',
            )
            ->withParsedBody([
                'id' => '22222222-2222-2222-2222-222222222222',
                'email' => 'existing@example.com',
                'firstName' => 'Other',
                'lastName' => 'User',
                'createdAt' => '2026-06-10 10:00:00',
                'updatedAt' => null,
                'deletedAt' => null,
            ]);

        $response = $this->app->handle($request);

        self::assertSame(409, $response->getStatusCode());

        (new OpenApiValidator())->validateResponse(
            '/users/{id}',
            'put',
            $response,
        );
    }

    public function testReturns422WhenValidationFails() : void {
        $this->loadSchema('users');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'user@example.com',
                'first_name' => 'User',
                'last_name' => 'Name',
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/users/550e8400-e29b-41d4-a716-446655440000',
            )
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'invalid-email',
                'firstName' => '',
                'lastName' => '',
                'createdAt' => '2026-06-10 10:00:00',
                'updatedAt' => null,
                'deletedAt' => null,
            ]);

        $response = $this->app->handle($request);

        self::assertSame(422, $response->getStatusCode());

        (new OpenApiValidator())->validateResponse(
            '/users/{id}',
            'put',
            $response,
        );
    }
}
