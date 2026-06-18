<?php

declare(strict_types=1);

namespace App\Application\Handlers\User;

use App\Domain\ValueObjects\UserId;

class DeleteUserHandler extends UserHandler {
    public function handle(UserId $id) : void {
        $this->userRepository->softDelete($id);
    }
}
