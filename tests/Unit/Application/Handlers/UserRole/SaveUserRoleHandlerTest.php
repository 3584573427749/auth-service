<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\UserRole;

use App\Application\Commands\UserRole\UserRoleCommand;
use App\Application\Handlers\UserRole\SaveUserRoleHandler;
use App\Domain\Entities\UserRole;
use App\Domain\Repositories\UserRoleRepository;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class SaveUserRoleHandlerTest extends TestCase {
    public function testHandleCreatesAndSavesUserRole() : void {
        $command = UserRoleCommand::fromRequest(
            [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'roleId' => '660e8400-e29b-41d4-a716-446655440000', ],
        );

        $repository = $this->createMock(UserRoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    static function (UserRole $userRole) : bool {
                        return
                            (string)$userRole->getUserId()
                            === '550e8400-e29b-41d4-a716-446655440000'
                            &&
                            (string)$userRole->getRoleId()
                            === '660e8400-e29b-41d4-a716-446655440000';
                    },
                ),
            );

        $handler = new SaveUserRoleHandler(
            $this->createMock(Connection::class),
            $repository,
        );

        $handler->handle($command);
    }
}
