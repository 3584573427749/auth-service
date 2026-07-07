<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Role;

use Doctrine\DBAL\Exception;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class DeleteRoleEndpointTest extends BaseApiTestCases {
    /**
     * @throws Exception
     */
    public function testReturns204WhenRoleIsDeleted() : void {
        $this->loadSchema('roles');

        $this->seed('roles', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Test Role',
                'description' => 'A test role',
                'admin_level' => 1,
                'created_at' => '2026-06-10 10:00:00',
                'updated_at' => '2026-06-10 10:00:00',
            ],
        ]);
        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/roles/550e8400-e29b-41d4-a716-446655440000',
            );

        $response = $this->app->handle($request);

        self::assertSame(204, $response->getStatusCode());
        self::assertSame('', (string)$response->getBody());

        $count = $this->connection
            ->executeQuery(
                'SELECT COUNT(*) FROM roles WHERE id = ?',
                ['550e8400-e29b-41d4-a716-446655440000'],
            )
            ->fetchOne();

        self::assertSame(0, (int)$count);

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/roles/{id}',
            'delete',
            $response,
        );
    }

    public function testReturns404WhenRoleDoesNotExist() : void {
        $this->loadSchema('roles');

        $request = new ServerRequestFactory()
            ->createServerRequest(
                'DELETE',
                '/roles/550e8400-e29b-41d4-a716-446655440000',
            );

        $response = $this->app->handle($request);

        self::assertSame(
            404,
            $response->getStatusCode(),
        );

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/roles/{id}',
            'delete',
            $response,
        );
    }
}
