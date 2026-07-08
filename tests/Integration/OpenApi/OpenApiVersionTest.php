<?php

declare(strict_types=1);

namespace Tests\OpenApi;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class OpenApiVersionTest extends TestCase {
    public function testOpenApiVersionMatchesVersionFile() : void {
        $version = trim(file_get_contents(__DIR__ . '/../../../VERSION') ?: '');

        $openApi = Yaml::parseFile(
            __DIR__ . '../../../../openapi.yaml',
        );

        self::assertArrayHasKey('info', $openApi);
        self::assertArrayHasKey('version', $openApi['info']);

        self::assertSame(
            $version,
            (string)$openApi['info']['version'],
            sprintf('openapi.yaml version (%s) does not match VERSION file (%s)', $openApi['info']['version'], $version),
        );
    }
}
