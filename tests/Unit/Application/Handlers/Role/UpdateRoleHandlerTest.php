<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\Role;

use App\Application\Commands\Role\UpdateRoleCommand;
use App\Application\Commands\User\UpdateUserCommand;
use App\Application\Handlers\Roles\UpdateRoleHandler;
use App\Application\Handlers\User\UpdateUserHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use App\Domain\Exception\RoleAlreadyExistsException;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Repositories\RoleRepository;
use App\Domain\Repositories\UserRepository;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class UpdateRoleHandlerTest extends TestCase {
    public function testHandleUpdateRoleSuccessfully() : void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(RoleRepository::class);

        $command = UpdateRoleCommand::fromRequest(
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Test role',
                'description' => 'Test Role',
                'adminLevel' => '1',
                'createdAt' => '2026-01-01 10:00:00',
            ],
        );

        $role = Role::fromDBRow([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'Test role',
            'description' => 'Test Role',
            'admin_level' => '1',
            'created_at' => '2026-01-01 10:00:00',
        ]);

        $db->expects(self::once())->method('beginTransaction');
        $db->expects(self::once())->method('commit');
        $db->expects(self::never())->method('rollBack');

        $repository
            ->expects(self::once())
            ->method('getById')
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->willReturn($role);

        $repository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(function ($role) {
                return $role->getName() === 'Test role';
            }));

        $handler = new class($db, $repository) extends UpdateRoleHandler {
            public function __construct(Connection $db, RoleRepository $repository) {
                $this->db = $db;
                $this->repository = $repository;
            }
        };

        $result = $handler->handle($command);

        self::assertInstanceOf(RoleDTO::class, $result);

        $json = $result->jsonSerialize();

        self::assertSame('Test role', $json['name']);
        self::assertSame('Test Role', $json['description']);
    }

    public function testHandleThrowsExceptionIfRoleNameExists() : void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(RoleRepository::class);

        $command = UpdateRoleCommand::fromRequest(
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Test role',
                'description' => 'Test Role',
                'isActive' => true,
                'createdAt' => '2026-01-01 10:00:00',
            ],
        );

        $db->expects(self::once())->method('beginTransaction');
        $db->expects(self::never())->method('commit');
        $db->expects(self::once())->method('rollBack');

        $repository
            ->expects(self::once())
            ->method('getById')
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->willThrowException(new RoleAlreadyExistsException('Test role'));

        $handler = new class($db, $repository) extends UpdateRoleHandler {
            public function __construct(Connection $db, RoleRepository $repository) {
                $this->db = $db;
                $this->repository = $repository;
            }
        };

        self::expectException(RoleAlreadyExistsException::class);

        $handler->handle($command);
    }

    public function testHandleRollsBackOnSaveError() : void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(RoleRepository::class);

        $command = UpdateRoleCommand::fromRequest(
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Test role',
                'description' => 'Test Role',
                'adminLevel' => true,
                'createdAt' => '2026-01-01 10:00:00',
            ],
        );

        $role = Role::fromDBRow([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'Test role',
            'description' => 'Test Role',
            'admin_level' => '1',
            'created_at' => '2026-01-01 10:00:00',
        ]);

        $db->expects(self::once())->method('beginTransaction');
        $db->expects(self::never())->method('commit');
        $db->expects(self::once())->method('rollBack');

        $repository
            ->expects(self::once())
            ->method('getById')
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->willReturn($role);

        $repository
            ->expects(self::once())
            ->method('save')
            ->willThrowException(new \RuntimeException('DB error'));

        $handler = new class($db, $repository) extends UpdateRoleHandler {
            public function __construct(Connection $db, RoleRepository $repository) {
                $this->db = $db;
                $this->repository = $repository;
            }
        };

        self::expectException(\RuntimeException::class);

        $handler->handle($command);
    }
}
