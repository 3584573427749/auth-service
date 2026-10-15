<?php

declare(strict_types=1);

namespace Application\Handlers\Auth;

use App\Application\Commands\Auth\VerifyOneTimeCodeCommand;
use App\Application\Handlers\Auth\VerifyOneTimeCodeHandler;
use App\Domain\DataTransportObjects\User\UserDTO;
use App\Domain\Entities\OneTimeCode;
use App\Domain\Entities\User;
use App\Domain\Exception\ValidationException;
use App\Domain\Repositories\OneTimeCodeRepository;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\OneTimeCodeId;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class VerifyOneTimeCodeHandlerTest extends TestCase {
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
            new DateTimeValue('2026-01-01 00:00:00'),
            null,
            null,
        );
    }

    public function testHandleReturnsUserWhenCodeIsValid() : void {
        $user = $this->createUser();

        $code = new OneTimeCode(
            new OneTimeCodeId(),
            $user->getId(),
            hash('sha256', '123456'),
            new DateTimeValue('+10 minutes'),
            null,
            new DateTimeValue('now'),
        );

        $userRepository = $this->createMock(
            UserRepository::class,
        );

        $userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with('test@example.com')
            ->willReturn($user);

        $repository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $repository
            ->expects($this->once())
            ->method('getActiveCodeByUserId')
            ->with($user->getId())
            ->willReturn($code);

        $repository
            ->expects($this->once())
            ->method('save')
            ->with($code);

        $handler = new VerifyOneTimeCodeHandler(
            $userRepository,
            $repository,
        );

        $result = $handler->handle(
            new VerifyOneTimeCodeCommand(
                new Email('test@example.com'),
                '123456',
            ),
        );

        self::assertInstanceOf(
            UserDTO::class,
            $result,
        );
    }

    public function testHandleThrowsValidationExceptionWhenUserDoesNotExist() : void {
        $userRepository = $this->createMock(
            UserRepository::class,
        );

        $userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->willReturn(null);

        $repository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $handler = new VerifyOneTimeCodeHandler(
            $userRepository,
            $repository,
        );

        $this->expectException(
            ValidationException::class,
        );

        $handler->handle(
            new VerifyOneTimeCodeCommand(
                new Email('missing@example.com'),
                '123456',
            ),
        );
    }

    public function testHandleThrowsValidationExceptionWhenCodeIsInvalid() : void {
        $user = $this->createUser();

        $code = new OneTimeCode(
            new OneTimeCodeId(),
            $user->getId(),
            hash('sha256', '123456'),
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

        $repository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $repository
            ->method('getActiveCodeByUserId')
            ->willReturn($code);

        $repository
            ->expects($this->never())
            ->method('save');

        $handler = new VerifyOneTimeCodeHandler(
            $userRepository,
            $repository,
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

        $code = new OneTimeCode(
            new OneTimeCodeId(),
            $user->getId(),
            hash('sha256', '123456'),
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

        $repository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $repository
            ->method('getActiveCodeByUserId')
            ->willReturn($code);

        $handler = new VerifyOneTimeCodeHandler(
            $userRepository,
            $repository,
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

        $code = new OneTimeCode(
            new OneTimeCodeId(),
            $user->getId(),
            hash('sha256', '123456'),
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

        $repository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $repository
            ->method('getActiveCodeByUserId')
            ->willReturn($code);

        $handler = new VerifyOneTimeCodeHandler(
            $userRepository,
            $repository,
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
}
