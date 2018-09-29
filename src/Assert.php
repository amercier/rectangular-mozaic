<?php

namespace RectangularMozaic;

use InvalidArgumentException;

/**
 * Various assertions.
 */
class Assert
{
    /**
     * Assert an integer is positive.
     *
     * @param int $value Value to test.
     * @param string $name Name of the argument.
     * @return void
     * @throws InvalidArgumentException if `$value` is lower than 0.
     */
    public static function assertPositiveInteger(int $value, string $name)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException("Expecting {$name} to be a positive integer, got {$value}");
        }
    }

    /**
     * Assert an integer is either positive or zero.
     *
     * @param int $value Value to test.
     * @param string $name Name of the argument.
     * @return void
     * @throws InvalidArgumentException if `$value` is lower than 0.
     */
    public static function assertNonNegativeInteger(int $value, string $name)
    {
        if ($value < 0) {
            throw new InvalidArgumentException("Expecting {$name} not to be a negative number, got {$value}");
        }
    }

    /**
     * Assert an integer is between two known values
     *
     * @param int $min Minimun acceptable value.
     * @param int $max Maximum acceptable value.
     * @param int $value Value to test.
     * @param string $name Name of the argument.
     * @return void
     * @throws InvalidArgumentException if `$value` is lower than `$min` or higher than `$max`.
     */
    public static function assertIntegerBetween(int $min, int $max, int $value, string $name)
    {
        if ($value < $min || $value > $max) {
            throw new InvalidArgumentException("Expecting {$name} to be between {$min} and {$max}, got {$value}");
        }
    }
}
