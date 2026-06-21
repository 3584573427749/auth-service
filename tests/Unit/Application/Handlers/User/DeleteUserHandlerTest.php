<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\User;

use App\Application\Handlers\User\DeleteUserHandler;
use App\Domain\Repositories\UserRepository;
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

        $handler = new class($db, $repository) extends DeleteUserHandler {
            public function __construct(Connection $db, UserRepository $userRepository) {
                $this->db = $db;
                $this->userRepository = $userRepository;
            }
        };

        $handler->handle($userId);
    }
}
