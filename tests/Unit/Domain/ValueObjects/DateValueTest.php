<?php

use App\Domain\ValueObject\DateValue;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DateValueTest extends TestCase {
    public function testValidDate():void {
        $date = new DateValue('2025-04-05');

        $this->assertSame('2025-04-05', (string)$date);
    }

    public function testInvalidDateFormat():void {
        $this->expectException(InvalidArgumentException::class);

        new DateValue('05-04-2025');
    }

    public function testInvalidCalendarDate():void {
        $this->expectException(InvalidArgumentException::class);

        new DateValue('2025-13-33');
    }
}