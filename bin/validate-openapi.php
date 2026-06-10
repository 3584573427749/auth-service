<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use cebe\openapi\Reader;

$openapi = Reader::readFromYamlFile(
    __DIR__ . '/../openapi.yaml'
);

if ($openapi === null) {
    fwrite(STDERR, "Failed to parse OpenAPI file.\n");
    exit(1);
}

$errors = $openapi->getErrors();

if ($errors !== []) {
    foreach ($errors as $error) {
        fwrite(STDERR, $error . PHP_EOL);
    }

    exit(1);
}

echo "OpenAPI validation successful.\n";