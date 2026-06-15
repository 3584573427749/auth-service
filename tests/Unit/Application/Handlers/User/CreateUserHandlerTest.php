<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\User;

use App\Application\Commands\User\CreateUserCommand;
use App\Application\Handlers\User\CreateUserHandler;
use App\Domain\DataTransportObjects\User\CreateUserDTO;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Repositories\UserRepository;
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

        $handler = new class($db, $repository) extends CreateUserHandler {
            public function __construct(Connection $db, UserRepository $userRepository) {
                $this->db = $db;
                $this->userRepository = $userRepository;
            }
        };

        $result = $handler->handle($command);

        self::assertInstanceOf(CreateUserDTO::class, $result);

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

        $handler = new class($db, $repository) extends CreateUserHandler {
            public function __construct(Connection $db, UserRepository $userRepository) {
                $this->db = $db;
                $this->userRepository = $userRepository;
            }
        };

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
                $this->userRepository = $userRepository;
            }
        };

        self::expectException(\RuntimeException::class);

        $handler->handle($command);
    }
}
