<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\User;

use App\Application\Commands\User\CreateUserCommand;
use App\Application\Handlers\User\CreateUserHandler;
use App\Application\Handlers\UserRole\SaveUserRoleHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class CreateUserHandlerTest extends TestCase {
    public function testHandleCreatesUserSuccessfully() : void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $command = CreateUserCommand::fromRequest(
            [
                'email' => 'test@example.com',
                'firstName' => 'User',
                'lastName' => 'Name'],
        );

        $db->expects(self::once())->method('beginTransaction');
        $db->expects(self::once())->method('commit');
        $db->expects(self::never())->method('rollBack');

        $repository
            ->expects(self::once())
            ->method('existsByEmail')
            ->with('test@example.com')
            ->willReturn(false);

        $repository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(function ($user) {
                return $user->getEmail()->toString() === 'test@example.com';
            }));

        $saveUserRoleHandler = $this->createMock(SaveUserRoleHandler::class);
        $saveUserRoleHandler
            ->expects(self::once())
            ->method('handleSaveAll')
            ->with(self::isInstanceOf(UserId::class), self::equalTo([]));

        $handler = new CreateUserHandler($db, $repository, $saveUserRoleHandler);

        $result = $handler->handle($command);

        self::assertInstanceOf(UserDTO::class, $result);

        $json = $result->jsonSerialize();

        self::assertSame('test@example.com', $json['email']);
        self::assertSame('User', $json['firstName']);
        self::assertSame('Name', $json['lastName']);
    }

    public function testHandleThrowsExceptionIfUserExists() : void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $command = CreateUserCommand::fromRequest(
            [
                'email' => 'test@example.com',
                'firstName' => 'User',
                'lastName' => 'Name'],
        );

        $db->expects(self::once())->method('beginTransaction');
        $db->expects(self::never())->method('commit');
        $db->expects(self::once())->method('rollBack');

        $repository
            ->expects(self::once())
            ->method('existsByEmail')
            ->willReturn(true);

        $saveUserRoleHandler = $this->createMock(SaveUserRoleHandler::class);
        $saveUserRoleHandler
            ->expects(self::never())
            ->method('handleSaveAll');

        $handler = new CreateUserHandler($db, $repository, $saveUserRoleHandler);

        self::expectException(UserAlreadyExistsException::class);

        $handler->handle($command);
    }

    public function testHandleRollsBackOnSaveError() : void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $command = CreateUserCommand::fromRequest(
            [
                'email' => 'test@example.com',
                'firstName' => 'User',
                'lastName' => 'Name'],
        );

        $db->expects(self::once())->method('beginTransaction');
        $db->expects(self::never())->method('commit');
        $db->expects(self::once())->method('rollBack');

        $repository
            ->expects(self::once())
            ->method('existsByEmail')
            ->willReturn(false);

        $repository
            ->expects(self::once())
            ->method('save')
            ->willThrowException(new \RuntimeException('DB error'));

        $handler = new class($db, $repository) extends CreateUserHandler {
            public function __construct(Connection $db, UserRepository $userRepository) {
                $this->db = $db;
                $this->repository = $userRepository;
            }
        };

        self::expectException(\RuntimeException::class);

        $handler->handle($command);
    }

    public function testHandleRollsBackWhenSavingRolesFails() : void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $command = CreateUserCommand::fromRequest([
            'email' => 'test@example.com',
            'firstName' => 'User',
            'lastName' => 'Name',
            'roles' => [
                '660e8400-e29b-41d4-a716-446655440000',
            ],
        ]);

        $db->expects($this->once())
            ->method('beginTransaction');

        $db->expects($this->never())
            ->method('commit');

        $db->expects($this->once())
            ->method('rollBack');

        $repository
            ->expects($this->once())
            ->method('existsByEmail')
            ->willReturn(false);

        $repository
            ->expects($this->once())
            ->method('save');

        $saveUserRoleHandler = $this->createMock(
            SaveUserRoleHandler::class,
        );

        $saveUserRoleHandler
            ->expects($this->once())
            ->method('handleSaveAll')
            ->willThrowException(
                new \RuntimeException('Role error'),
            );

        $handler = new CreateUserHandler(
            $db,
            $repository,
            $saveUserRoleHandler,
        );

        $this->expectException(
            \RuntimeException::class,
        );

        $handler->handle($command);
    }
}
