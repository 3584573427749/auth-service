<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\User;

use App\Application\Handlers\User\DeleteUserHandler;
use App\Application\Handlers\UserRole\DeleteUserRoleHandler;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\UserRoleRepository;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Database\DbalUserRepository;
use PHPUnit\Framework\TestCase;

final class DeleteUserHandlerTest extends TestCase {
    public function testHandleCallsSoftDelete() : void {
        $userId = new UserId(
            '550e8400-e29b-41d4-a716-446655440000',
        );

        $repository = $this->createMock(DbalUserRepository::class);

        $repository
            ->expects($this->once())
            ->method('softDelete')
            ->with($userId);

        $userRoleRepository = $this->createMock(UserRoleRepository::class);

        $deleteUserRoleHandler = $this->createMock(DeleteUserRoleHandler::class);
        $deleteUserRoleHandler
            ->expects($this->once())
            ->method('deleteAll')
            ->with($userId);

        $handler = new DeleteUserHandler($repository, $deleteUserRoleHandler);

        $handler->softDelete($userId);
    }

    public function testHandleThrowsNotFound() : void {
        $userId = new UserId(
            '550e8400-e29b-41d4-a716-446655440000',
        );

        $repository = $this->createMock(DbalUserRepository::class);
        $repository->expects($this->once())
            ->method('rollBack');
        $repository->expects(self::never())
            ->method('commit');

        $repository
            ->expects($this->once())
            ->method('softDelete')
            ->willThrowException(new NotFoundException('User not found'));

        $userRoleRepository = $this->createMock(UserRoleRepository::class);

        $deleteUserRoleHandler = $this->createMock(DeleteUserRoleHandler::class);
        $deleteUserRoleHandler
            ->expects($this->once())
            ->method('deleteAll')
            ->with($userId);

        $handler = new DeleteUserHandler($repository, $deleteUserRoleHandler);

        $this->expectException(NotFoundException::class);

        $handler->softDelete($userId);
    }

    public function testHandleCallsRemove() : void {
        $userId = new UserId(
            '550e8400-e29b-41d4-a716-446655440000',
        );

        $repository = $this->createMock(DbalUserRepository::class);

        $repository
            ->expects($this->once())
            ->method('remove')
            ->with($userId);

        $userRoleRepository = $this->createMock(UserRoleRepository::class);

        $deleteUserRoleHandler = $this->createMock(DeleteUserRoleHandler::class);
        $deleteUserRoleHandler
            ->expects($this->once())
            ->method('deleteAll')
            ->with($userId);

        $handler = new DeleteUserHandler($repository, $deleteUserRoleHandler);

        $handler->removeUser($userId);
    }

    public function testRemoveThrowsNotFound() : void {
        $userId = new UserId(
            '550e8400-e29b-41d4-a716-446655440000',
        );

        $repository = $this->createMock(DbalUserRepository::class);

        $repository
            ->expects($this->once())
            ->method('remove')
            ->with($userId)
            ->willThrowException(new NotFoundException('User not found'));

        $userRoleRepository = $this->createMock(UserRoleRepository::class);

        $deleteUserRoleHandler = $this->createMock(DeleteUserRoleHandler::class);
        $deleteUserRoleHandler
            ->expects($this->once())
            ->method('deleteAll')
            ->with($userId);

        $repository->expects($this->once())
            ->method('rollBack');
        $repository->expects(self::never())
            ->method('commit');

        $handler = new DeleteUserHandler($repository, $deleteUserRoleHandler);

        $this->expectException(NotFoundException::class);

        $handler->removeUser($userId);
    }
}
