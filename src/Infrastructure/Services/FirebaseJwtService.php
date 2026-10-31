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
        private int $refreshLifetime,
    ) {
    }

    /**
     * @param array<int, Role> $roles
     */
    public function createAccessToken(User $user, array $roles) : string {
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
            'type' => 'access',
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
    public function verify(string $token) : array {
        return (array)JWT::decode(
            $token,
            new Key(
                $this->secret,
                'HS256',
            ),
        );
    }

    public function createRefreshToken(User $user) : string {
        $issuedAt = time();

        $payload = [
            'sub' => $user->getId()->toString(),
            'type' => 'refresh',
            'iss' => $this->issuer,
            'iat' => $issuedAt,
            'exp' => $issuedAt + $this->refreshLifetime,
        ];

        return JWT::encode(
            $payload,
            $this->secret,
            'HS256',
        );
    }

    public function isAccessToken(string $token) : bool {
        return $this->verify($token)['type'] === 'access';
    }

    public function isRefreshToken(
        string $token,
    ) : bool {
        return $this->verify($token)['type'] === 'refresh';
    }
}
