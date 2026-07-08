<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\Role;

use App\Application\Commands\Role\CreateRoleCommand;
use App\Application\Handlers\Roles\CreateRoleHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\Repositories\RoleRepository;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class CreateRoleHandlerTest extends TestCase {
    public function testHandleCreatesRoleSuccessfully(): void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(RoleRepository::class);

        $command = CreateRoleCommand::fromRequest(
            [
                'name' => 'Test',
                'description' => 'Test role',
                'adminLevel' => 1],
        );

        $db->expects(self::once())->method('beginTransaction');
        $db->expects(self::once())->method('commit');
        $db->expects(self::never())->method('rollBack');

        $repository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(function ($role) {
                return $role->getName() === 'Test';
            }));

        $handler = new class($db, $repository) extends CreateRoleHandler {
            public function __construct(Connection $db, RoleRepository $roleRepository) {
                $this->db = $db;
                $this->repository = $roleRepository;
            }
        };

        $result = $handler->handle($command);

        self::assertInstanceOf(RoleDTO::class, $result);

        $json = $result->jsonSerialize();

        self::assertSame('Test', $json['name']);
        self::assertSame('Test role', $json['description']);
        self::assertSame(1, $json['adminLevel']);
    }

    public function testHandleRollsBackOnSaveError(): void {
        $db = $this->createMock(Connection::class);
        $repository = $this->createMock(RoleRepository::class);

        $command = CreateRoleCommand::fromRequest(
            [
                'name' => 'Test',
                'description' => 'Test role',
                'adminLevel' => 1],
        );

        $db->expects(self::once())->method('beginTransaction');
        $db->expects(self::never())->method('commit');
        $db->expects(self::once())->method('rollBack');

        $repository
            ->expects(self::once())
            ->method('save')
            ->willThrowException(new \RuntimeException('DB error'));

        $handler = new class($db, $repository) extends CreateRoleHandler {
            public function __construct(Connection $db, RoleRepository $roleRepository) {
                $this->db = $db;
                $this->repository = $roleRepository;
            }
        };

        self::expectException(\RuntimeException::class);

        $handler->handle($command);
    }
}
