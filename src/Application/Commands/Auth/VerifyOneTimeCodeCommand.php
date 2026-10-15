<?php

declare(strict_types=1);

namespace App\Application\Commands\Auth;

use App\Domain\ValueObjects\Email;

class VerifyOneTimeCodeCommand {
    public function __construct(
        public Email $email,
        public string $code,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromRequest(array $data) : self {
        return new self(
            new Email(strtolower(trim((string)$data['email']))),
            trim((string)$data['code']),
        );
    }
}
