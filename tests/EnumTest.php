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
        $this->assertSame(((string)Letters::A()), 'A');
        $this->assertSame(((string)Letters::B()), 'B');
    }

    public function testToValueReturnsNullWhenNullIsGiven()
    {
        $this->assertSame(null, Letters::toValue(null));
    }

    public function testToValueReturnsValueWhenBoolIsGiven()
    {
        $this->assertSame(true, Letters::toValue(true));
        $this->assertSame(false, Letters::toValue(false));
    }

    public function testToValueReturnsValueWhenEnumIsGiven()
    {
        $this->assertSame(Letters::A, Letters::toValue(Letters::A()));
        $this->assertSame(Letters::B, Letters::toValue(Letters::B()));
    }
}
