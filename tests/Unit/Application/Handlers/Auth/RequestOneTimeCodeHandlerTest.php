<?php

declare(strict_types=1);

namespace Application\Handlers\Auth;

use App\Application\Commands\Auth\RequestOneTimeCodeCommand;
use App\Application\Handlers\Auth\RequestOneTimeCodeHandler;
use App\Domain\DataTransportObjects\Auth\LoginDTO;
use App\Domain\Entities\OneTimeCode;
use App\Domain\Entities\User;
use App\Domain\Repositories\OneTimeCodeRepository;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class RequestOneTimeCodeHandlerTest extends TestCase {
    public function testHandleReturnsSuccessWhenUserDoesNotExist() : void {
        $command = new RequestOneTimeCodeCommand(
            'missing@example.com',
        );

        $userRepository = $this->createMock(
            UserRepository::class,
        );

        $userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->with('missing@example.com')
            ->willReturn(null);

        $repository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $repository
            ->expects($this->never())
            ->method('save');


        $handler = new RequestOneTimeCodeHandler(
            $userRepository,
            $repository,
        );

        $response = $handler->handle($command);

        self::assertInstanceOf(
            LoginDTO::class,
            $response,
        );
    }

    public function testHandleCreatesOneTimeCode() : void {
        $user = $this->createUser();

        $userRepository = $this->createMock(
            UserRepository::class,
        );

        $userRepository
            ->expects($this->once())
            ->method('getByEmail')
            ->willReturn($user);

        $repository = $this->createMock(
            OneTimeCodeRepository::class,
        );

        $repository
            ->expects($this->once())
            ->method('invalidateActiveCodes')
            ->with($user->getId());

        $repository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->isInstanceOf(
                    OneTimeCode::class,
                ),
            );

        $handler = new RequestOneTimeCodeHandler(
            $userRepository,
            $repository,
        );

        $handler->handle(
            new RequestOneTimeCodeCommand(
                'test@example.com',
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

    public function testCreatedCodeBelongsToUser() : void {
        $user = $this->createUser();

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
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    static function (
                        OneTimeCode $code,
                    ) use ($user) : bool {
                        return
                            $code->getUserId()
                                ->toString()
                            ===
                            $user->getId()
                                ->toString();
                    },
                ),
            );

        $handler = new RequestOneTimeCodeHandler($userRepository, $repository, );

        $handler->handle(new RequestOneTimeCodeCommand('test@example.com'));
    }

    public function testHandleThrowsExceptionWhenSaveFails() : void {
        $user = $this->createUser();

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
            ->expects($this->once())
            ->method('save')
            ->willThrowException(
                new \RuntimeException(
                    'Database error',
                ),
            );

        $handler = new RequestOneTimeCodeHandler(
            $userRepository,
            $repository,
        );

        $this->expectException(
            \RuntimeException::class,
        );

        $handler->handle(
            new RequestOneTimeCodeCommand(
                'test@example.com',
            ),
        );
    }
}
