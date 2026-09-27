<?php

declare(strict_types=1);

namespace App\Application\Clients\GroupService;

use App\Domain\ValueObjects\UserId;

/**
 * Skapar eller uppdaterar en ledare i Group Service.
 *
 * active=true  -> ledare ska vara aktiv
 * active=false -> ledare ska vara avaktiverad
 */
interface GroupServiceClient {
    public function syncLeader(
        UserId $userId,
        string $firstName,
        string $lastName,
        bool $active,
    ) : void;
}
