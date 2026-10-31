<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Commands\Auth;

use App\Application\Commands\Auth\RefreshAccessTokenCommand;
use PHPUnit\Framework\TestCase;

final class RefreshAccessTokenCommandTest extends TestCase {
    public function testFromRequest() : void {
        $command = RefreshAccessTokenCommand::fromRequest([
            'refreshToken' => 'refresh-token',
        ]);

        self::assertSame(
            'refresh-token',
            $command->refreshToken,
        );
    }

    public function testConstructor() : void {
        $command = new RefreshAccessTokenCommand(
            'refresh-token',
        );

        self::assertSame(
            'refresh-token',
            $command->refreshToken,
        );
    }
}
