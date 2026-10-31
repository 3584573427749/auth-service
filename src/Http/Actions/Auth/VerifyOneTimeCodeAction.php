<?php

declare(strict_types=1);

namespace App\Http\Actions\Auth;

use App\Application\Commands\Auth\VerifyOneTimeCodeCommand;
use App\Application\Handlers\Auth\VerifyOneTimeCodeHandler;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class VerifyOneTimeCodeAction extends Action {
    public function __construct(LoggerInterface $logger, private VerifyOneTimeCodeHandler $handler) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        $command = VerifyOneTimeCodeCommand::fromRequest(
            (array)$this->request->getParsedBody(),
        );

        $otcDTO = $this->handler->handle($command, );

        return $this->respondWithData(
            $otcDTO,
        );
    }
}
