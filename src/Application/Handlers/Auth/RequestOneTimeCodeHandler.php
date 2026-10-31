<?php


declare(strict_types=1);

namespace App\Application\Handlers\Auth;

use App\Application\Commands\Auth\RequestOneTimeCodeCommand;
use App\Domain\DataTransportObjects\Auth\LoginDTO;
use App\Domain\Entities\OneTimeCode;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\OneTimeCodeId;

class RequestOneTimeCodeHandler extends OneTimeCodeHandler {
    public function handle(RequestOneTimeCodeCommand $command) : LoginDTO {
        $email = new Email($command->email);

        $user = $this->userRepository->getByEmail($email);

        if ($user === null) {
            return LoginDTO::success();
        }

        $this->oneTimeCodeRepository->invalidateActiveCodes($user->getId());

        $otp = (string)random_int(100000, 999999);

        $oneTimeCode = new OneTimeCode(
            new OneTimeCodeId(),
            $user->getId(),
            (string) $otp, //hash('sha256', $otp), // Annars kan vi inte veta vilket lösenordet är!
            new DateTimeValue('+10 minutes'),
            null,
            new DateTimeValue('now'),
        );

        $this->oneTimeCodeRepository->save($oneTimeCode);

        // TODO:
        // $this->mailService->sendOtp(
        //     $user->getEmail(),
        //     $otp
        // );

        return LoginDTO::success();
    }
}
