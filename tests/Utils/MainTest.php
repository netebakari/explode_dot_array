<?php
namespace netebakari\Tests\Utils;

use PHPUnit\Framework\TestCase;
use netebakari\Utils\Main;

class MainTest extends TestCase
{
    public function testAddReturnsSumOfTwoNumbers(): void
    {
        // 1. 準備 (Arrange)
        $num1 = 5;
        $num2 = 10;
        $expected = 15;

        // 2. 実行 (Act)
        $actual = Main::add($num1, $num2);

        // 3. 検証 (Assert)
        $this->assertSame($expected, $actual, '5 + 10 != 15');
    }

    public function testAddWithNegativeNumbers(): void
    {
        $this->assertSame(-2, Main::add(3, -5));
        $this->assertSame(-8, Main::add(-3, -5));
    }
}