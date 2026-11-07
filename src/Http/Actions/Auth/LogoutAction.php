<?php

declare(strict_types=1);

namespace App\Http\Actions\Auth;

use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class LogoutAction extends Action {
    public function __construct(
        LoggerInterface $logger,
    ) {
        parent::__construct($logger);
    }

    protected function action() : Response {
        return $this->respondWithData([
            'message' => 'Utloggad',
        ])->withAddedHeader(
            'Set-Cookie',
            'refreshToken=; Path=/auth/refresh; Max-Age=0; HttpOnly; Secure; SameSite=Strict',
        );
    }
}
