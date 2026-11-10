<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Services\MailService;
use Psr\Log\LoggerInterface;

class AppLogMailService implements MailService {
    public function __construct(private LoggerInterface $logger) {
    }

    public function sendOneTimeCode(string $email, string $code) : void {
        $this->logger->info(
            'Sending one-time code',
            [
                'recipient' => $email,
                'code' => $code,
            ],
        );
    }
}
