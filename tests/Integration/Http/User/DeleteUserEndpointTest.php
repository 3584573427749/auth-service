<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Users;

use Doctrine\DBAL\Exception;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class DeleteUserEndpointTest extends BaseApiTestCases {
    /**
     * @throws Exception
     */
    public function testReturns204WhenUserExists() : void {
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
            ->createServerRequest(
                'DELETE',
                '/users/550e8400-e29b-41d4-a716-446655440000',
            );

        $response = $this->app->handle($request);

        self::assertSame(
            204,
            $response->getStatusCode(),
        );

        self::assertSame(
            '',
            (string)$response->getBody(),
        );

        $count = $this->connection
            ->executeQuery(
                'SELECT COUNT(*) FROM users WHERE id = ? and is_active = 1',
                ['550e8400-e29b-41d4-a716-446655440000'],
            )
            ->fetchOne();

        self::assertSame(0, (int)$count);

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}',
            'delete',
            $response,
        );
    }

    public function testReturns404WhenUserDoesNotExist() : void {
        $this->loadSchema('users');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/users/550e8400-e29b-41d4-a716-446655440000',
            );

        $response = $this->app->handle($request);

        self::assertSame(
            404,
            $response->getStatusCode(),
        );

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}',
            'delete',
            $response,
        );
    }
}
