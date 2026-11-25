<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Application\Clients\GroupService\GroupServiceClient;
use App\Domain\ValueObjects\UserId;

class FakeGroupServiceClient implements GroupServiceClient {
    public function syncLeader(UserId $userId, string $firstName, string $lastName, bool $active) : void {
    }
}
