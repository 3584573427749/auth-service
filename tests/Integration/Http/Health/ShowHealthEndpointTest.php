<?php


declare(strict_types=1);

namespace Tests\Integration\Http\Health;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class ShowHealthEndpointTest extends BaseApiTestCases {
    public function testReturns200() : void {
        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/health',
            );

        $response = $this->app->handle($request);

        self::assertSame(
            200,
            $response->getStatusCode(),
        );

        $payload = json_decode(
            (string) $response->getBody(),
            true,
        );

        self::assertIsArray($payload);

        self::assertArrayHasKey(
            'data',
            $payload,
        );

        self::assertIsArray(
            $payload['data'],
        );

        self::assertArrayHasKey(
            'status',
            $payload['data'],
        );

        self::assertArrayHasKey(
            'service',
            $payload['data'],
        );

        self::assertArrayHasKey(
            'version',
            $payload['data'],
        );

        self::assertArrayHasKey(
            'checks',
            $payload['data'],
        );

        self::assertSame(
            'auth-service',
            $payload['data']['service'],
        );

        self::assertContains(
            $payload['data']['status'],
            ['ok', 'degraded'],
        );

        self::assertIsArray(
            $payload['data']['checks'],
        );

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/health',
            'get',
            $response,
        );
    }
}
