<?php

namespace RectangularMozaic;

use MyCLabs\Enum\Enum as EnumBase;

/**
 * Enumeration.
 */
abstract class Enum extends EnumBase
{
    /**
     * Disallow creating instance using `new`.
     */
    protected function __construct($value)
    {
        parent::__construct($value);
    }

    /**
     * String representation of the enum value.
     */
    public function __toString()
    {
        return $this->getKey();
    }

    /**
     * Get the value from a mixed value.
     *
     * @param mixed $value Enum instance, or anything else.
     * @return int The enum value if `$value` is an instance of Enum, or
     * @return mixed The given value unchanged, otherwise.
     */
    public static function toValue($value)
    {
        return $value instanceof Enum ? $value->getValue() : $value;
    }
}
