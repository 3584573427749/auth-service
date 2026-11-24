<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\Role;

use App\Application\Commands\Role\UpdateRoleCommand;
use App\Application\Handlers\Role\UpdateRoleHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\Entities\Role;
use App\Domain\Exception\RoleAlreadyExistsException;
use App\Infrastructure\Database\DbalRoleRepository;
use PHPUnit\Framework\TestCase;

final class UpdateRoleHandlerTest extends TestCase {
    public function testHandleUpdateRoleSuccessfully() : void {
        $repository = $this->createMock(DbalRoleRepository::class);

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

        $handler = new UpdateRoleHandler($repository);

        $result = $handler->handle($command);

        self::assertInstanceOf(RoleDTO::class, $result);

        $json = $result->jsonSerialize();

        self::assertSame('Test role', $json['name']);
        self::assertSame('Test Role', $json['description']);
    }

    public function testHandleThrowsExceptionIfRoleNameExists() : void {
        $repository = $this->createMock(DbalRoleRepository::class);

        $command = UpdateRoleCommand::fromRequest(
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Test role',
                'description' => 'Test Role',
                'createdAt' => '2026-01-01 10:00:00',
            ],
        );

        $repository
            ->expects(self::once())
            ->method('getById')
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->willThrowException(new RoleAlreadyExistsException('Test role'));

        $handler = new UpdateRoleHandler($repository);

        self::expectException(RoleAlreadyExistsException::class);

        $handler->handle($command);
    }

    public function testHandleRollsBackOnSaveError() : void {
        $repository = $this->createMock(DbalRoleRepository::class);

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

        $repository
            ->expects(self::once())
            ->method('getById')
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->willReturn($role);

        $repository
            ->expects(self::once())
            ->method('save')
            ->willThrowException(new \RuntimeException('DB error'));

        $handler = new UpdateRoleHandler($repository);

        self::expectException(\RuntimeException::class);

        $handler->handle($command);
    }
}
