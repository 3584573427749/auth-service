<?php


declare(strict_types=1);

namespace Tests\Integration\Config;

use PHPUnit\Framework\TestCase;

final class EnvironmentFilesTest extends TestCase {
    public function testEnvironmentFilesContainSameNumberOfKeys(): void {
        $referenceKeys = $this->getKeys('.env');

        $environmentFiles = $this->getEnvironmentFiles();

        foreach ($environmentFiles as $file) {
            self::assertCount(
                count($referenceKeys),
                $this->getKeys($file),
                sprintf(
                    '%s does not contain the same number of keys as .env',
                    $file
                )
            );
        }
    }

    public function testEnvironmentFilesContainSameKeysAsEnv(): void {
        $referenceKeys = $this->getKeys('.env');

        sort($referenceKeys);

        $files = array_filter(
            glob('.env.*') ?: [],
            static fn(string $file): bool => is_file($file)
        );

        foreach ($files as $file) {
            $keys = $this->getKeys($file);

            sort($keys);

            self::assertSame(
                $referenceKeys,
                $keys,
                sprintf(
                    '%s does not contain the same keys as .env',
                    $file
                )
            );
        }
    }
    /**
     * @return string[]
     */
    private function getKeys(string $file): array {
        self::assertFileExists($file);

        $keys = [];

        $lines = file(
            $file,
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );

        if ($lines === false) {
            return [];
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if (
                $line === ''
                || str_starts_with($line, '#')
                || !str_contains($line, '=')
            ) {
                continue;
            }

            [$key] = explode('=', $line, 2);

            $keys[] = trim($key);
        }

        return $keys;
    }
    /**
     * @return string[]
     */
    private function getEnvironmentFiles(): array {
        return array_filter(
            glob('.env.*') ?: [],
            static fn(string $file): bool => is_file($file)
        );
    }
}