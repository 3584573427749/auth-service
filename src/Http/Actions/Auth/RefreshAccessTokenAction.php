<?php

declare(strict_types=1);

namespace App\Http\Actions\Auth;

use App\Application\Commands\Auth\RefreshAccessTokenCommand;
use App\Application\Handlers\Auth\RefreshAccessTokenHandler;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class RefreshAccessTokenAction extends Action {
    public function __construct(LoggerInterface $logger, private RefreshAccessTokenHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $command = RefreshAccessTokenCommand::fromRequest((array)$this->request->getParsedBody());

        $response = $this->handler->handle($command);

        return $this->respondWithData($response);
    }
}
