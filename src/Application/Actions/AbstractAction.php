<?php

declare(strict_types=1);

namespace App\Application\Actions;

use Psr\Http\Message\ResponseInterface as Response;

abstract class AbstractAction {
    protected function respond(Response $response, array $data, int $status = 200): Response {
        $payload = [
            'status' => $status,
            'data' => $data,
        ];

        $response->getBody()->write(json_encode($payload));

        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }
}
