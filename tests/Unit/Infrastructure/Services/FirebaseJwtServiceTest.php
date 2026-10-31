<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Services;

use App\Domain\Entities\Role;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Services\FirebaseJwtService;
use PHPUnit\Framework\TestCase;

final class FirebaseJwtServiceTest extends TestCase {
    public function testCreateAccessTokenReturnsJwt() : void {
        $service = new FirebaseJwtService(
            str_repeat('secret', 10),
            'auth-service',
            900,
        );

        $token = $service->createAccessToken(
            $this->createUser(),
            [
                $this->createRole(),
            ],
        );

        self::assertNotEmpty($token);

        self::assertCount(
            3,
            explode('.', $token),
        );
    }

    public function testVerifyReturnsExpectedClaims() : void {
        $service = new FirebaseJwtService(
            str_repeat('secret', 10),
            'auth-service',
            900,
        );

        $token = $service->createAccessToken(
            $this->createUser(),
            [
                $this->createRole(),
            ],
        );

        $claims = $service->verify($token);

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $claims['sub'],
        );

        self::assertSame(
            'test@example.com',
            $claims['email'],
        );

        self::assertSame(
            'User',
            $claims['firstName'],
        );

        self::assertSame(
            'Name',
            $claims['lastName'],
        );

        self::assertSame(
            ['Leader'],
            $claims['roles'],
        );

        self::assertSame(
            'auth-service',
            $claims['iss'],
        );

        self::assertArrayHasKey(
            'iat',
            $claims,
        );

        self::assertArrayHasKey(
            'exp',
            $claims,
        );
    }

    public function testVerifyThrowsExceptionForInvalidToken() : void {
        $service = new FirebaseJwtService(
            'secret',
            'auth-service',
            900,
        );

        $this->expectException(\Throwable::class);

        $service->verify(
            'this-is-not-a-valid-token',
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

    private function createRole() : Role {
        return new Role(
            new RoleId(),
            'Leader',
            'Leader role',
            10,
            new DateTimeValue(
                '2026-01-01 00:00:00',
            ),
            null,
        );
    }
}
