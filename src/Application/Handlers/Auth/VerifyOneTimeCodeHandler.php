<?php

declare(strict_types=1);

namespace App\Application\Handlers\Auth;

use App\Application\Commands\Auth\VerifyOneTimeCodeCommand;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Exception\ValidationException;
use App\Domain\Repositories\OneTimeCodeRepository;
use App\Domain\Repositories\UserRepository;

class VerifyOneTimeCodeHandler {
    public function __construct(
        private UserRepository $userRepository,
        private OneTimeCodeRepository $oneTimeCodeRepository,
    ) {
    }

    public function handle(VerifyOneTimeCodeCommand $command) : UserDTO {
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

        $hash = hash(
            'sha256',
            $command->code,
        );

        if ($hash !== $oneTimeCode->getCodeHash()) {
            throw new ValidationException(
                'Ogiltig kod',
            );
        }

        $oneTimeCode->consume();

        $this->oneTimeCodeRepository->save(
            $oneTimeCode,
        );

        return UserDTO::fromUser($user);
    }
}
