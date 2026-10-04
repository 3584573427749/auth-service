<?php

declare(strict_types=1);

namespace App\Domain\DataTransportObjects\Login;

use App\Domain\DataTransportObjects\User\UserDTO;

class VerifyOtpDTO implements \JsonSerializable {
    public function __construct(
        private string $accessToken,
        private string $refreshToken,
        private UserDTO $user,
    ) {
    }

    /**
     * @return array<string ,mixed>
     */
    public function jsonSerialize() : array {
        return [
            'accessToken' => $this->accessToken,
            'refreshToken' => $this->refreshToken,
            'user' => $this->user,
        ];
    }
}
