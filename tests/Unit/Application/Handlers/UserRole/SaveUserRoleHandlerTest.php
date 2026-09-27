<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\UserRole;

use App\Application\Clients\GroupService\GroupServiceClient;
use App\Application\Commands\UserRole\UserRoleCommand;
use App\Application\Handlers\UserRole\SaveUserRoleHandler;
use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use App\Domain\Entities\UserRole;
use App\Domain\Exception\GroupServiceUnavailableException;
use App\Domain\Repositories\RoleRepository;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\UserRoleRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
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

        $userRepository = $this->createMock(UserRepository::class);
        $roleRepository = $this->createMock(RoleRepository::class);
        $groupServiceClient = $this->createMock(GroupServiceClient::class);

        $handler = new SaveUserRoleHandler(
            $this->createMock(Connection::class),
            $repository,
            $userRepository,
            $roleRepository,
            $groupServiceClient,
        );

        $handler->handle($command);
    }

    public function testLeaderRoleTriggersGroupServiceSync() : void {
        $command = UserRoleCommand::fromRequest([
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'roleId' => '660e8400-e29b-41d4-a716-446655440000',
        ]);

        $connection = $this->createMock(Connection::class);

        $connection
            ->expects($this->once())
            ->method('beginTransaction');

        $connection
            ->expects($this->once())
            ->method('commit');

        $repository = $this->createMock(UserRoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('save');

        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('test@example.com'),
            'User',
            'Name',
            new DateTimeValue('2026-01-01 00:00:00'),
            null,
            null,
        );

        $role = new Role(
            new RoleId('660e8400-e29b-41d4-a716-446655440000'),
            Role::LEADER,
            'Leader role',
            10,
            new DateTimeValue('2026-01-01 00:00:00'),
            null,
        );

        $userRepository = $this->createMock(UserRepository::class);

        $userRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($user);

        $roleRepository = $this->createMock(RoleRepository::class);

        $roleRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($role);

        $groupServiceClient = $this->createMock(GroupServiceClient::class);

        $groupServiceClient
            ->expects($this->once())
            ->method('syncLeader')
            ->with(
                $user->getId(),
                'User',
                'Name',
                true,
            );

        $handler = new SaveUserRoleHandler(
            $connection,
            $repository,
            $userRepository,
            $roleRepository,
            $groupServiceClient,
        );

        $handler->handle($command);
    }

    public function testRollsBackTransactionWhenGroupServiceFails() : void {
        $command = UserRoleCommand::fromRequest([
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'roleId' => '660e8400-e29b-41d4-a716-446655440000',
        ]);

        $connection = $this->createMock(Connection::class);

        $connection
            ->expects($this->once())
            ->method('beginTransaction');

        $connection
            ->expects($this->never())
            ->method('commit');

        $connection
            ->expects($this->once())
            ->method('rollBack');

        $repository = $this->createMock(UserRoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('save');

        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('test@example.com'),
            'User',
            'Name',
            new DateTimeValue('2026-01-01 00:00:00'),
            null,
            null,
        );

        $role = new Role(
            new RoleId('660e8400-e29b-41d4-a716-446655440000'),
            Role::LEADER,
            'Leader role',
            10,
            new DateTimeValue('2026-01-01 00:00:00'),
            null,
        );

        $userRepository = $this->createMock(UserRepository::class);

        $userRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($user);

        $roleRepository = $this->createMock(RoleRepository::class);

        $roleRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($role);

        $groupServiceClient = $this->createMock(GroupServiceClient::class);

        $groupServiceClient
            ->expects($this->once())
            ->method('syncLeader')
            ->willThrowException(
                new GroupServiceUnavailableException(
                    'Group Service unavailable',
                ),
            );

        $handler = new SaveUserRoleHandler(
            $connection,
            $repository,
            $userRepository,
            $roleRepository,
            $groupServiceClient,
        );

        $this->expectException(
            GroupServiceUnavailableException::class,
        );

        $handler->handle($command);
    }
}
