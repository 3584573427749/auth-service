<?php

declare(strict_types=1);

namespace App\Application\Handlers\Auth;

use App\Application\Commands\Auth\RefreshAccessTokenCommand;
use App\Application\Services\JwtService;
use App\Domain\DataTransportObjects\Auth\AuthenticationDTO;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\Role;
use App\Domain\Exception\ValidationException;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\UserRoleRepository;
use App\Domain\ValueObjects\UserId;
use Firebase\JWT\ExpiredException;

class RefreshAccessTokenHandler {
    public function __construct(private JwtService $jwtService, private UserRepository $userRepository, private UserRoleRepository $userRoleRepository) {

    }

    public function handle(RefreshAccessTokenCommand $command) : AuthenticationDTO {
        try {
            $claims = $this->jwtService->verify($command->refreshToken);

            if ($claims['type'] !== 'refresh') {
                throw new ValidationException('Invalid refresh token', );
            }

            $user = $this->userRepository->getById(new UserId($claims['sub']));

            $roles = $this->userRoleRepository->getRoles($user->getId());

            $accessToken = $this->jwtService->createAccessToken($user, $roles);

            $refreshToken = $this->jwtService->createRefreshToken($user);

            return new AuthenticationDTO(
                $accessToken,
                $refreshToken,
                UserDTO::fromUser($user)->withRoles(
                    array_map(
                        static fn (Role $role) : string => $role->getName(),
                        $roles,
                    ),
                ),
            );
        } catch (ExpiredException) {
            throw new ValidationException(
                'Refresh token has expired',
            );
        }
    }
}
