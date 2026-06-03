<?php

declare(strict_types=1);

namespace App\Application\Actions\Health;

use App\Application\Actions\AbstractAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HealthAction extends AbstractAction {
    public function __invoke(Request $request, Response $response): Response {
        return $this->respond($response, [
            'health' => [
                'status' => 'ok',
            ],
        ]);
    }
}
