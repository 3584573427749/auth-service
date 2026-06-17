<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\User;

use App\Application\Handlers\User\GetUserHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\User;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class GetUserHandlerTest extends TestCase {
    public function testGetAllReturnsUserDTOs() : void {
        $user1 = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('a@test.com'),
            'A',
            'User',
            true,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $user2 = new User(
            new UserId('660e8400-e29b-41d4-a716-446655440000'),
            new Email('b@test.com'),
            'B',
            'User',
            true,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$user1, $user2]);

        $handler = new class($repository) extends GetUserHandler {
            public function __construct(UserRepository $userRepository) {
                $this->userRepository = $userRepository;
            }
        };

        $result = $handler->getAll();

        self::assertCount(2, $result);

        self::assertInstanceOf(UserDTO::class, $result[0]);
        self::assertInstanceOf(UserDTO::class, $result[1]);

    }

    public function testGetAllReturnsEmptyArrayWhenNoUsers() : void {
        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $handler = new class($repository) extends GetUserHandler {
            public function __construct(UserRepository $userRepository) {
                $this->userRepository = $userRepository;
            }
        };

        $result = $handler->getAll();

        self::assertSame([], $result);
    }

    public function testGetByIdReturnsUserDTO() : void {
        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new Email('test@example.com'),
            'User',
            'Name',
            true,
            new DateTimeValue('2026-01-01 10:00:00'),
            null,
        );

        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($user);

        $handler = new class($repository) extends GetUserHandler {
            public function __construct(UserRepository $repo) {
                $this->userRepository = $repo;
            }
        };

        $result = $handler->getById(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
        );

        self::assertInstanceOf(UserDTO::class, $result);

        $data = $result->jsonSerialize();

        self::assertSame('test@example.com', $data['email']);
        self::assertSame('User', $data['firstName']);
        self::assertSame('Name', $data['lastName']);
    }

    public function testGetByIdThrowsUserNotFoundException() : void {
        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects($this->once())
            ->method('getById')
            ->willThrowException(new NotFoundException('User not found'));

        $handler = new class($repository) extends GetUserHandler {
            public function __construct(UserRepository $repo) {
                $this->userRepository = $repo;
            }
        };

        $this->expectException(NotFoundException::class);

        $handler->getById(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
        );
    }
}
