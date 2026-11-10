<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Services;

use App\Infrastructure\Services\AppLogMailService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class AppLogMailServiceTest extends TestCase {
    public function testSendOneTimeCodeLogsMessage() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $logger
            ->expects($this->once())
            ->method('info')
            ->with(
                'Sending one-time code',
                [
                    'recipient' => 'test@example.com',
                    'code' => '123456',
                ],
            );

        $service = new AppLogMailService($logger, );

        $service->sendOneTimeCode('test@example.com', '123456');
    }
}
