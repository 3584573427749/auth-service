<?php

declare(strict_types=1);

namespace App\Application\Commands\Auth;

class RequestOneTimeCodeCommand {
    public function __construct(public string $email) {
    }

    /**
     * @param array<string, string> $data
     */
    public static function fromRequest(array $data) : self {
        return new self(strtolower(trim($data['email'])));
    }
}
