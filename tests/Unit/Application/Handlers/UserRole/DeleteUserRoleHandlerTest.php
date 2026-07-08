<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\UserRole;

use App\Application\Commands\UserRole\UserRoleCommand;
use App\Application\Handlers\UserRole\DeleteUserRoleHandler;
use App\Domain\Entities\UserRole;
use App\Domain\Repositories\UserRoleRepository;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class DeleteUserRoleHandlerTest extends TestCase {
    public function testHandleDelete() : void {
        $command = UserRoleCommand::fromRequest([
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'roleId' => '550e8400-e29b-41d4-a716-446655440001',
        ]);
        $userRole = new UserRole(new UserId($command->userId), new RoleId($command->roleId));

        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('delete')
            ->with($userRole);

        $handler = new class($db, $repository) extends DeleteUserRoleHandler {
            public function __construct(Connection $db, UserRoleRepository $repository) {
                $this->db = $db;
                $this->repository = $repository;
            }
        };

        $handler->handle($command);
    }
}
