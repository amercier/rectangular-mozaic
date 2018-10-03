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
}
