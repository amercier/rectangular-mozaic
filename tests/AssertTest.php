<?php

namespace RectangularMozaic\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RectangularMozaic\Assert;

class AssertTest extends TestCase
{
    public function testAssertPositiveIntegerFailsWhenValueIsNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::assertPositiveInteger(-1, 'minusOne');
    }

    public function testAssertPositiveIntegerFailsWhenValueIsZero()
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::assertPositiveInteger(0, 'zero');
    }

    public function testAssertPositiveIntegerSucceedsAndReturnVoidWhenValueIsOne()
    {
        $this->assertEquals(null, Assert::assertPositiveInteger(1, 'one'));
    }

    public function testAssertPositiveIntegerSucceedsAndReturnVoidWhenValueIsMaxInteger()
    {
        $this->assertEquals(null, Assert::assertPositiveInteger(PHP_INT_MAX, 'maxInt'));
    }

    public function testAssertNonNegativeIntegerFailsWhenValueIsNegative()
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::assertNonNegativeInteger(-1, 'minusOne');
    }

    public function testAssertNonNegativeIntegerFailsWhenValueIsZero()
    {
        $this->assertEquals(null, Assert::assertNonNegativeInteger(0, 'zero'));
    }

    public function testAssertNonNegativeIntegerSucceedsAndReturnVoidWhenValueIsOne()
    {
        $this->assertEquals(null, Assert::assertNonNegativeInteger(1, 'one'));
    }

    public function testAssertNonNegativeIntegerSucceedsAndReturnVoidWhenValueIsMaxInteger()
    {
        $this->assertEquals(null, Assert::assertNonNegativeInteger(PHP_INT_MAX, 'maxInt'));
    }

    public function testAssertIntegerBetweenFailsWhenValueIsBelowMinimum()
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::assertIntegerBetween(10, 20, 9, 'below');
    }

    public function testAssertIntegerBetweenFailsWhenValueIsAboveMaximum()
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::assertIntegerBetween(10, 20, 21, 'above');
    }

    public function testAssertIntegerBetweenSucceedsAndReturnVoidWhenValueIsMinimum()
    {
        $this->assertEquals(null, Assert::assertIntegerBetween(10, 20, 10, 'min'));
    }

    public function testAssertIntegerBetweenSucceedsAndReturnVoidWhenValueIsMaximum()
    {
        $this->assertEquals(null, Assert::assertPositiveInteger(10, 20, 20, 'max'));
    }
}
