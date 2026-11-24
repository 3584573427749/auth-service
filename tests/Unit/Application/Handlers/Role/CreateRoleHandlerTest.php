<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\Role;

use App\Application\Commands\Role\CreateRoleCommand;
use App\Application\Handlers\Role\CreateRoleHandler;
use App\Domain\DataTransportObjects\Role\RoleDTO;
use App\Domain\Exception\RoleAlreadyExistsException;
use App\Infrastructure\Database\DbalRoleRepository;
use PHPUnit\Framework\TestCase;

final class CreateRoleHandlerTest extends TestCase {
    public function testHandleCreatesRoleSuccessfully() : void {
        $repository = $this->createMock(DbalRoleRepository::class);

        $command = CreateRoleCommand::fromRequest(
            [
                'name' => 'Test',
                'description' => 'Test role',
                'adminLevel' => 1],
        );

        $repository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(function ($role) {
                return $role->getName() === 'Test';
            }));

        $handler = new CreateRoleHandler($repository) ;

        $result = $handler->handle($command);

        self::assertInstanceOf(RoleDTO::class, $result);

        $json = $result->jsonSerialize();

        self::assertSame('Test', $json['name']);
        self::assertSame('Test role', $json['description']);
        self::assertSame(1, $json['adminLevel']);
    }

    public function testHandleRollsBackOnSaveError() : void {
        $repository = $this->createMock(DbalRoleRepository::class);

        $command = CreateRoleCommand::fromRequest(
            [
                'name' => 'Test',
                'description' => 'Test role',
                'adminLevel' => 1],
        );

        $repository
            ->expects(self::once())
            ->method('save')
            ->willThrowException(new \RuntimeException('DB error'));

        $handler = new CreateRoleHandler($repository) ;

        self::expectException(\RuntimeException::class);

        $handler->handle($command);
    }

    public function testHandleRollsBackWhenRoleAlreadyExists() : void {
        $repository = $this->createMock(DbalRoleRepository::class);

        $command = CreateRoleCommand::fromRequest([
            'name' => 'Test',
            'description' => 'Test role',
            'adminLevel' => 1,
        ]);

        $repository
            ->expects(self::once())
            ->method('save')
            ->willThrowException(
                new RoleAlreadyExistsException(
                    'Rollen finns redan',
                ),
            );

        $handler = new CreateRoleHandler($repository) ;

        self::expectException(
            RoleAlreadyExistsException::class,
        );

        $handler->handle($command);
    }
}
