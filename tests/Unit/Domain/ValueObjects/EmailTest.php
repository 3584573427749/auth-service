<?php

use App\Domain\ValueObject\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase {
    public function testValidEmailIsNormalized():void {
        $email = new Email('  TEST@Example.COM ');

        $this->assertSame('test@example.com', $email->toString());
    }

    public function testInvalidEmailThrowsException():void {
        $this->expectException(InvalidArgumentException::class);

        new Email('not-an-email');
    }

    public function testEquals():void {
        $a = new Email('a@test.com');
        $b = new Email('A@test.com');

        $this->assertTrue($a->equals($b));
    }
}