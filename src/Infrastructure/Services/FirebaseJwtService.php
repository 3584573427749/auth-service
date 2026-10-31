<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Services\JwtService;
use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class FirebaseJwtService implements JwtService {
    public function __construct(
        private string $secret,
        private string $issuer,
        private int $lifetime,
    ) {
    }

    /**
     * @param array<int, Role> $roles
     */
    public function createAccessToken(
        User $user,
        array $roles,
    ) : string {
        $issuedAt = time();

        $payload = [
            'sub' => $user->getId()->toString(),
            'email' => $user->getEmail()->toString(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'roles' => array_map(
                static fn (Role $role) : string => $role->getName(),
                $roles,
            ),
            'iss' => $this->issuer,
            'iat' => $issuedAt,
            'exp' => $issuedAt + $this->lifetime,
        ];

        echo $this->secret;
        return JWT::encode(
            $payload,
            $this->secret,
            'HS256',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function verify(
        string $token,
    ) : array {
        return (array) JWT::decode(
            $token,
            new Key(
                $this->secret,
                'HS256',
            ),
        );
    }
}
