<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\Role;

use App\Application\Handlers\Role\DeleteRoleHandler;
use App\Domain\Repositories\RoleRepository;
use App\Domain\ValueObjects\RoleId;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class DeleteRoleHandlerTest extends TestCase {
    public function testHandleCallsSoftDelete() : void {
        $roleId = new RoleId(
            '550e8400-e29b-41d4-a716-446655440000',
        );

        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(RoleRepository::class);

        $repository
            ->expects($this->once())
            ->method('delete')
            ->with($roleId);

        $handler = new class($db, $repository) extends DeleteRoleHandler {
            public function __construct(Connection $db, RoleRepository $roleRepository) {
                $this->db = $db;
                $this->repository = $roleRepository;
            }
        };

        $handler->handle($roleId);
    }
}
