<?php

declare(strict_types=1);

namespace App\Application\Services;

interface MailService {
    public function sendOneTimeCode(
        string $email,
        string $code,
    ) : void;
}
