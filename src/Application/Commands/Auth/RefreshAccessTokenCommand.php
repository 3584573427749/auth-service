<?php

declare(strict_types=1);

namespace App\Application\Commands\Auth;

class RefreshAccessTokenCommand {
    public function __construct(
        public string $refreshToken,
    ) {
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromRequest(array $data) : self {
        return new self(
            (string) $data['refreshToken'],
        );
    }
}
