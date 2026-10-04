<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\OneTimeCode;
use App\Domain\ValueObjects\OneTimeCodeId;
use App\Domain\ValueObjects\UserId;

interface OneTimeCodeRepository {
    public function save(
        OneTimeCode $code,
    ) : void;

    public function getById(OneTimeCodeId $id) : OneTimeCode;

    /**
     * Hämtar den senaste aktiva OTP-koden för användaren.
     */
    public function getActiveCodeByUserId(UserId $userId) : OneTimeCode;

    /**
     * Markerar samtliga aktiva OTP-koder för användaren som förbrukade.
     */
    public function invalidateActiveCodes(UserId $userId) : void;
}
