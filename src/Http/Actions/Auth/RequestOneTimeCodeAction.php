<?php


declare(strict_types=1);

namespace App\Http\Actions\Auth;

use App\Application\Commands\Auth\RequestOneTimeCodeCommand;
use App\Application\Handlers\Auth\RequestOneTimeCodeHandler;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class RequestOneTimeCodeAction extends Action {
    public function __construct(LoggerInterface $logger, private RequestOneTimeCodeHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $command = RequestOneTimeCodeCommand::fromRequest((array)$this->request->getParsedBody());

        $response = $this->handler->handle($command);

        return $this->respondWithData($response);
    }
}
