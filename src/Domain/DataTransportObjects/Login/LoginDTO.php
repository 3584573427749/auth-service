<?php

declare(strict_types=1);

namespace App\Domain\DataTransportObjects\Login;

class LoginDTO implements \JsonSerializable {
    public function __construct(private string $message) {
    }

    public static function success() : self {
        return new self('Om användaren finns har information skickats.');
    }

    /**
     * @return string[]
     */
    public function jsonSerialize() : array {
        return ['message' => $this->message, ];
    }
}
