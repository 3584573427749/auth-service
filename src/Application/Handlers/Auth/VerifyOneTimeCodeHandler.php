<?php

declare(strict_types=1);

namespace App\Application\Handlers\Auth;

use App\Application\Commands\Auth\VerifyOneTimeCodeCommand;
use App\Application\Services\JwtService;
use App\Domain\DataTransportObjects\Auth\VerifyOneTimeCodeDTO;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\Role;
use App\Domain\Exception\ValidationException;
use App\Domain\Repositories\OneTimeCodeRepository;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\UserRoleRepository;

class VerifyOneTimeCodeHandler extends OneTimeCodeHandler {
    public function __construct(
        UserRepository $userRepository,
        OneTimeCodeRepository $oneTimeCodeRepository,
        private UserRoleRepository $userRoleRepository,
        private JwtService $jwtService,
    ) {
        parent::__construct($userRepository, $oneTimeCodeRepository);
    }

    public function handle(VerifyOneTimeCodeCommand $command) : VerifyOneTimeCodeDTO {
        $user = $this->userRepository->getByEmail(
            $command->email,
        );

        if ($user === null) {
            throw new ValidationException(
                'Ogiltig kod',
            );
        }

        $oneTimeCode = $this->oneTimeCodeRepository
            ->getActiveCodeByUserId(
                $user->getId(),
            );

        if ($oneTimeCode->isConsumed()) {
            throw new ValidationException(
                'Koden har redan använts',
            );
        }

        if ($oneTimeCode->isExpired()) {
            throw new ValidationException(
                'Koden har gått ut',
            );
        }

        /*        $hash = hash(
                    'sha256',
                    $command->code,
                );
        */
        $hash = $command->code;

        if ($hash !== $oneTimeCode->getCodeHash()) {
            throw new ValidationException(
                'Ogiltig kod',
            );
        }

        $oneTimeCode->consume();

        $this->oneTimeCodeRepository->save($oneTimeCode);

        $roles = $this->userRoleRepository->getRoles($user->getId());

        $accessToken = $this->jwtService->createAccessToken(
            $user,
            $roles,
        );

        return new VerifyOneTimeCodeDTO(
            $accessToken,
            'refreshtoken',
            UserDTO::fromUser($user)->withRoles(
                array_map(
                    static fn (Role $role) : string => $role->getName(),
                    $roles,
                ),
            ),
        );
    }
}
