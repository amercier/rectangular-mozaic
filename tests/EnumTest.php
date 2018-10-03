<?php
namespace RectangularMozaic\Tests;

use PHPUnit\Framework\TestCase;
use RectangularMozaic\Enum;

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
class Letters extends Enum
{
    const A = 1;
    const B = 2;
}

class EnumTest extends TestCase
{
    /**
     * @expectedException Error
     * @expectedExceptionMessageRegExp /Call to protected RectangularMozaic\\Enum::__construct/
     */
    public function testTileConstructorIsNotAccessible()
    {
        new Letters(Letters::A);
    }

    public function testToStringReturnsConstantName()
    {
        $this->assertEquals(((string)Letters::A()), 'A');
        $this->assertEquals(((string)Letters::B()), 'B');
    }
}
