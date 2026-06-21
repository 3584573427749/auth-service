<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Users;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class CreateUserEndpointTest extends BaseApiTestCases {
    public function testReturns201WhenRequestIsValid() : void {
        $this->loadSchema('users');

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/users')
            ->withParsedBody([
                'email' => 'test@example.com',
                'firstName' => 'User',
                'lastName' => 'Name',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(201, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users',
            'post',
            $response,
        );
    }

    public function testReturns409WhenUserAlreadyExists() : void {
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
            ->createServerRequest('POST', '/users')
            ->withParsedBody([
                'email' => 'test@example.com',
                'firstName' => 'User',
                'lastName' => 'Name',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(409, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users',
            'post',
            $response,
        );
    }

    public function testReturns422WhenValidationFails() : void {
        $this->loadSchema('users');

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/users')
            ->withParsedBody([
                'email' => 'invalid-email',
                'firstName' => '',
                'lastName' => '',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(422, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users',
            'post',
            $response,
        );
    }
}
