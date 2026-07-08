<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Role;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class CreateRoleEndpointTest extends BaseApiTestCases {
    public function testReturns201WhenRequestIsValid(): void {
        $this->loadSchema('roles');
        $requestBody = [
            'name' => 'Test',
            'description' => 'Test role',
            'adminLevel' => 1,
        ];

        $validator = new OpenApiValidator();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/roles')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(
            json_encode($requestBody, JSON_THROW_ON_ERROR),
        );

        $request = $request->withParsedBody($requestBody);

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        self::assertSame(201, $response->getStatusCode());

        $validator->validateResponse(
            '/roles',
            'post',
            $response,
        );
    }

    public function testReturns409WhenRoleAlreadyExists(): void {
        $this->loadSchema('roles');

        $this->seed('roles', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Test',
                'description' => 'Test role',
                'admin_level' => 1,
            ],
        ]);

        $requestBody = [
            'name' => 'Test',
            'description' => 'Test role',
            'adminLevel' => 1,
        ];
        $request = new ServerRequestFactory()
            ->createServerRequest('POST', '/roles')
            ->withParsedBody($requestBody);

        $response = $this->app->handle($request);

        self::assertSame(409, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/roles',
            'post',
            $response,
        );


    }

    public function testReturns422WhenValidationFails(): void {
        $this->loadSchema('roles');

        $request = new ServerRequestFactory()
            ->createServerRequest('POST', '/roles')
            ->withParsedBody([
                'name' => '',
                'description' => '',
                'adminLevel' => '',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(422, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/roles',
            'post',
            $response,
        );
    }
}
