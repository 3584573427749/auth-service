<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\Auth;

use App\Application\Commands\Auth\VerifyOneTimeCodeCommand;
use App\Application\Handlers\Auth\VerifyOneTimeCodeHandler;
use App\Application\Services\JwtService;
use App\Domain\DataTransportObjects\Auth\AuthenticationDTO;
use App\Domain\Entities\OneTimeCode;
use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use App\Domain\Exception\ValidationException;
use App\Domain\Repositories\OneTimeCodeRepository;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\UserRoleRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\OneTimeCodeId;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class VerifyOneTimeCodeHandlerTest extends TestCase {
    public function testHandleReturnsJwtAndUser() : void {
        $user = $this->createUser();

        $oneTimeCode = new OneTimeCode(
            new OneTimeCodeId(),
            $user->getId(),
            '123456',
            new DateTimeValue('+10 minutes'),
            null,
            new DateTimeValue('now'),
        );

        $roles = [
            new Role(
                new RoleId(),
                'Leader',
                'Leader role',
                10,
                new DateTimeValue('now'),
                null,
            ),
        ];

        $userRepository = $this->createMock(UserRepository::class);

        $userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with('test@example.com')
            ->willReturn($user);

        $oneTimeCodeRepository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $oneTimeCodeRepository
            ->expects($this->once())
            ->method('getActiveCodeByUserId')
            ->with($user->getId())
            ->willReturn($oneTimeCode);

        $oneTimeCodeRepository
            ->expects($this->once())
            ->method('save');

        $userRoleRepository = $this->createMock(
            UserRoleRepository::class,
        );

        $userRoleRepository
            ->expects($this->once())
            ->method('getRoles')
            ->with($user->getId())
            ->willReturn($roles);

        $jwtService = $this->createMock(
            JwtService::class,
        );

        $jwtService
            ->expects($this->once())
            ->method('createAccessToken')
            ->with($user, $roles)
            ->willReturn('jwt-token');

        $handler = new VerifyOneTimeCodeHandler(
            $userRepository,
            $oneTimeCodeRepository,
            $userRoleRepository,
            $jwtService,
        );

        $result = $handler->handle(
            new VerifyOneTimeCodeCommand(
                new Email('test@example.com'),
                '123456',
            ),
        );

        self::assertInstanceOf(
            AuthenticationDTO::class,
            $result,
        );
    }

    public function testHandleThrowsValidationExceptionWhenCodeIsInvalid() : void {
        $user = $this->createUser();

        $oneTimeCode = new OneTimeCode(
            new OneTimeCodeId(),
            $user->getId(),
            '123456',
            new DateTimeValue('+10 minutes'),
            null,
            new DateTimeValue('now'),
        );

        $userRepository = $this->createMock(
            UserRepository::class,
        );

        $userRepository
            ->method('getByEmail')
            ->willReturn($user);

        $oneTimeCodeRepository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $oneTimeCodeRepository
            ->method('getActiveCodeByUserId')
            ->willReturn($oneTimeCode);

        $handler = new VerifyOneTimeCodeHandler(
            $userRepository,
            $oneTimeCodeRepository,
            $this->createMock(UserRoleRepository::class),
            $this->createMock(JwtService::class),
        );

        $this->expectException(
            ValidationException::class,
        );

        $handler->handle(
            new VerifyOneTimeCodeCommand(
                new Email('test@example.com'),
                '999999',
            ),
        );
    }

    public function testHandleThrowsValidationExceptionWhenCodeIsConsumed() : void {
        $user = $this->createUser();

        $oneTimeCode = new OneTimeCode(
            new OneTimeCodeId(),
            $user->getId(),
            '123456',
            new DateTimeValue('+10 minutes'),
            new DateTimeValue('now'),
            new DateTimeValue('now'),
        );

        $userRepository = $this->createMock(
            UserRepository::class,
        );

        $userRepository
            ->method('getByEmail')
            ->willReturn($user);

        $oneTimeCodeRepository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $oneTimeCodeRepository
            ->method('getActiveCodeByUserId')
            ->willReturn($oneTimeCode);

        $handler = new VerifyOneTimeCodeHandler(
            $userRepository,
            $oneTimeCodeRepository,
            $this->createMock(UserRoleRepository::class),
            $this->createMock(JwtService::class),
        );

        $this->expectException(
            ValidationException::class,
        );

        $handler->handle(
            new VerifyOneTimeCodeCommand(
                new Email('test@example.com'),
                '123456',
            ),
        );
    }

    public function testHandleThrowsValidationExceptionWhenCodeIsExpired() : void {
        $user = $this->createUser();

        $oneTimeCode = new OneTimeCode(
            new OneTimeCodeId(),
            $user->getId(),
            '123456',
            new DateTimeValue('-10 minutes'),
            null,
            new DateTimeValue('now'),
        );

        $userRepository = $this->createMock(
            UserRepository::class,
        );

        $userRepository
            ->method('getByEmail')
            ->willReturn($user);

        $oneTimeCodeRepository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $oneTimeCodeRepository
            ->method('getActiveCodeByUserId')
            ->willReturn($oneTimeCode);

        $handler = new VerifyOneTimeCodeHandler(
            $userRepository,
            $oneTimeCodeRepository,
            $this->createMock(UserRoleRepository::class),
            $this->createMock(JwtService::class),
        );

        $this->expectException(
            ValidationException::class,
        );

        $handler->handle(
            new VerifyOneTimeCodeCommand(
                new Email('test@example.com'),
                '123456',
            ),
        );
    }

    public function testHandleThrowsValidationExceptionWhenUserIsNotFound() : void {
        $user = $this->createUser();

        $oneTimeCode = new OneTimeCode(
            new OneTimeCodeId(),
            $user->getId(),
            '123456',
            new DateTimeValue('-10 minutes'),
            null,
            new DateTimeValue('now'),
        );

        $userRepository = $this->createMock(
            UserRepository::class,
        );

        $userRepository
            ->method('getByEmail')
            ->willReturn(null);

        $oneTimeCodeRepository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $oneTimeCodeRepository
            ->expects($this->never())
            ->method('getActiveCodeByUserId');

        $handler = new VerifyOneTimeCodeHandler(
            $userRepository,
            $oneTimeCodeRepository,
            $this->createMock(UserRoleRepository::class),
            $this->createMock(JwtService::class),
        );

        $this->expectException(
            ValidationException::class,
        );

        $handler->handle(
            new VerifyOneTimeCodeCommand(
                new Email('test@example.com'),
                '123456',
            ),
        );
    }

    private function createUser() : User {
        return new User(
            new UserId(
                '550e8400-e29b-41d4-a716-446655440000',
            ),
            new Email(
                'test@example.com',
            ),
            'User',
            'Name',
            new DateTimeValue(
                '2026-01-01 00:00:00',
            ),
            null,
            null,
        );
    }
}
