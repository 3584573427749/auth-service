<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\User;

use App\Application\Handlers\User\DeleteUserHandler;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\UserRoleRepository;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class DeleteUserHandlerTest extends TestCase {
    public function testHandleCallsSoftDelete() : void {
        $userId = new UserId(
            '550e8400-e29b-41d4-a716-446655440000',
        );

        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('softDelete')
            ->with($userId);

        $userRoleRepository = $this->createMock(UserRoleRepository::class);
        $userRoleRepository
            ->expects($this->once())
            ->method('deleteByUser')
            ->with($userId);

        $handler = new class($db, $repository, $userRoleRepository) extends DeleteUserHandler {
            public function __construct(Connection $db, UserRepository $userRepository, UserRoleRepository $userRoleRepository) {
                $this->db = $db;
                $this->repository = $userRepository;
                $this->userRoleRepository = $userRoleRepository;
            }
        };

        $handler->softDelete($userId);
    }

    public function testHandleThrowsNotFound() : void {
        $userId = new UserId(
            '550e8400-e29b-41d4-a716-446655440000',
        );

        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('softDelete')
            ->willThrowException(new NotFoundException('User not found'));

        $userRoleRepository = $this->createMock(UserRoleRepository::class);
        $userRoleRepository
            ->expects($this->never())
            ->method('deleteByUser')
            ->with($userId);

        $db->expects($this->once())
            ->method('rollBack');
        $db->expects(self::never())
            ->method('commit');

        $handler = new class($db, $repository, $userRoleRepository) extends DeleteUserHandler {
            public function __construct(Connection $db, UserRepository $userRepository, UserRoleRepository $userRoleRepository) {
                $this->db = $db;
                $this->repository = $userRepository;
                $this->userRoleRepository = $userRoleRepository;
            }
        };

        $this->expectException(NotFoundException::class);

        $handler->softDelete($userId);
    }

    public function testHandleCallsRemove() : void {
        $userId = new UserId(
            '550e8400-e29b-41d4-a716-446655440000',
        );

        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('remove')
            ->with($userId);

        $userRoleRepository = $this->createMock(UserRoleRepository::class);
        $userRoleRepository
            ->expects($this->once())
            ->method('deleteByUser')
            ->with($userId);

        $handler = new class($db, $repository, $userRoleRepository) extends DeleteUserHandler {
            public function __construct(Connection $db, UserRepository $userRepository, UserRoleRepository $userRoleRepository) {
                $this->db = $db;
                $this->repository = $userRepository;
                $this->userRoleRepository = $userRoleRepository;
            }
        };

        $handler->removeUser($userId);
    }

    public function testRemoveThrowsNotFound() : void {
        $userId = new UserId(
            '550e8400-e29b-41d4-a716-446655440000',
        );

        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('remove')
            ->with($userId)
            ->willThrowException(new NotFoundException('User not found'));

        $userRoleRepository = $this->createMock(UserRoleRepository::class);
        $userRoleRepository
            ->expects($this->once())
            ->method('deleteByUser')
            ->with($userId);

        $db->expects($this->once())
            ->method('rollBack');
        $db->expects(self::never())
            ->method('commit');

        $handler = new class($db, $repository, $userRoleRepository) extends DeleteUserHandler {
            public function __construct(Connection $db, UserRepository $userRepository, UserRoleRepository $userRoleRepository) {
                $this->db = $db;
                $this->repository = $userRepository;
                $this->userRoleRepository = $userRoleRepository;
            }
        };
        $this->expectException(NotFoundException::class);

        $handler->removeUser($userId);
    }
}
