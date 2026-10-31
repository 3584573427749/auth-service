<?php

declare(strict_types=1);


use App\Application\Commands\Auth\RefreshAccessTokenCommand;
use App\Application\Handlers\Auth\RefreshAccessTokenHandler;
use App\Application\Services\JwtService;
use App\Domain\DataTransportObjects\Auth\AuthenticationDTO;
use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use App\Domain\Exception\ValidationException;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\UserRoleRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class RefreshAccessTokenHandlerTest extends TestCase {
    public function testHandleCreatesNewTokens() : void {
        $user = $this->createUser();

        $roles = [
            new Role(
                new RoleId(
                    '660e8400-e29b-41d4-a716-446655440000',
                ),
                'Leader',
                'Leader role',
                10,
                new DateTimeValue('now'),
                null,
            ),
        ];

        $jwtService = $this->createMock(JwtService::class);

        $jwtService
            ->expects($this->once())
            ->method('verify')
            ->with('refresh-token')
            ->willReturn([
                'sub' => '550e8400-e29b-41d4-a716-446655440000',
                'type' => 'refresh',
            ]);

        $jwtService
            ->expects($this->once())
            ->method('createAccessToken')
            ->with($user, $roles)
            ->willReturn('new-access-token');

        $jwtService
            ->expects($this->once())
            ->method('createRefreshToken')
            ->with($user)
            ->willReturn('new-refresh-token');

        $userRepository = $this->createMock(UserRepository::class);

        $userRepository
            ->expects($this->once())
            ->method('getById')
            ->willReturn($user);

        $userRoleRepository = $this->createMock(UserRoleRepository::class);

        $userRoleRepository
            ->expects($this->once())
            ->method('getRoles')
            ->with($user->getId())
            ->willReturn($roles);

        $handler = new RefreshAccessTokenHandler(
            $jwtService,
            $userRepository,
            $userRoleRepository,
        );

        $result = $handler->handle(
            new RefreshAccessTokenCommand(
                'refresh-token',
            ),
        );

        self::assertInstanceOf(AuthenticationDTO::class, $result);
    }

    public function testHandleThrowsExceptionWhenTokenIsNotRefresh() : void {
        $jwtService = $this->createMock(
            JwtService::class,
        );

        $jwtService
            ->expects($this->once())
            ->method('verify')
            ->willReturn([
                'sub' => '550e8400-e29b-41d4-a716-446655440000',
                'type' => 'access',
            ]);

        $handler = new RefreshAccessTokenHandler(
            $jwtService,
            $this->createMock(UserRepository::class),
            $this->createMock(UserRoleRepository::class),
        );

        $this->expectException(
            ValidationException::class,
        );

        $handler->handle(
            new RefreshAccessTokenCommand(
                'access-token',
            ),
        );
    }

    public function testHandleBubblesUpJwtException() : void {
        $jwtService = $this->createMock(
            JwtService::class,
        );

        $jwtService
            ->expects($this->once())
            ->method('verify')
            ->willThrowException(
                new RuntimeException(
                    'Invalid token',
                ),
            );

        $handler = new RefreshAccessTokenHandler(
            $jwtService,
            $this->createMock(UserRepository::class),
            $this->createMock(UserRoleRepository::class),
        );

        $this->expectException(
            RuntimeException::class,
        );

        $handler->handle(
            new RefreshAccessTokenCommand(
                'broken-token',
            ),
        );
    }

    private function createUser() : User {
        return new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            Email::fromString('test@example.com', ),
            'User',
            'Name',
            new DateTimeValue('2026-01-01 00:00:00', ),
            null,
            null,
        );
    }
}
