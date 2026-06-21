<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\User;

use App\Application\Commands\User\UpdateUserCommand;
use App\Application\Handlers\User\UpdateUserHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Repositories\UserRepository;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class UpdateUserHandlerTest extends TestCase {
    public function testHandleUpdateUserSuccessfully() : void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(UserRepository::class);

        $command = UpdateUserCommand::fromRequest(
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'firstName' => 'User',
                'lastName' => 'Name',
                'isActive' => '1',
                'createdAt' => '2026-01-01 10:00:00',
            ],
        );

        $user=User::fromDBRow([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com',
            'first_name' => 'User',
            'last_name' => 'Name',
            'is_active' => '1',
            'created_at' => '2026-01-01 10:00:00',
        ]);

        $db->expects(self::once())->method('beginTransaction');
        $db->expects(self::once())->method('commit');
        $db->expects(self::never())->method('rollBack');

        $repository
            ->expects(self::once())
            ->method('emailExistsWithOtherUser')
            ->with('test@example.com', '550e8400-e29b-41d4-a716-446655440000')
            ->willReturn(false);

        $repository
            ->expects(self::once())
            ->method('getById')
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->willReturn($user);

        $repository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(function ($user) {
                return $user->getEmail()->toString() === 'test@example.com';
            }));

        $handler = new class($db, $repository) extends UpdateUserHandler {
            public function __construct(Connection $db, UserRepository $userRepository) {
                $this->db = $db;
                $this->userRepository = $userRepository;
            }
        };

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

        $command = UpdateUserCommand::fromRequest(
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'firstName' => 'User',
                'lastName' => 'Name',
                'isActive' => true,
                'createdAt' => '2026-01-01 10:00:00',
            ],
        );

        $db->expects(self::once())->method('beginTransaction');
        $db->expects(self::never())->method('commit');
        $db->expects(self::once())->method('rollBack');

        $repository
            ->expects(self::once())
            ->method('emailExistsWithOtherUser')
            ->with('test@example.com', '550e8400-e29b-41d4-a716-446655440000')
            ->willReturn(true);

        $handler = new class($db, $repository) extends UpdateUserHandler {
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

        $command = UpdateUserCommand::fromRequest(
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'email' => 'test@example.com',
                'firstName' => 'User',
                'lastName' => 'Name',
                'isActive' => true,
                'createdAt' => '2026-01-01 10:00:00',
            ],
        );

        $db->expects(self::once())->method('beginTransaction');
        $db->expects(self::never())->method('commit');
        $db->expects(self::once())->method('rollBack');

        $repository
            ->expects(self::once())
            ->method('emailExistsWithOtherUser')
            ->with('test@example.com', '550e8400-e29b-41d4-a716-446655440000')
            ->willReturn(false);

        $repository
            ->expects(self::once())
            ->method('save')
            ->willThrowException(new \RuntimeException('DB error'));

        $handler = new class($db, $repository) extends UpdateUserHandler {
            public function __construct(Connection $db, UserRepository $userRepository) {
                $this->db = $db;
                $this->userRepository = $userRepository;
            }
        };

        self::expectException(\RuntimeException::class);

        $handler->handle($command);
    }
}
