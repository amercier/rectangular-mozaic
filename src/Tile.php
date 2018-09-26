<?php

namespace RectangularMozaic;

/**
 * Tile format. Either:
 * - small: occupy 1 grid cell
 * - tall: occupy 2 grid cells vertically
 * - wide: occupy 2 grid cells horizontally
 */
class Tile extends Enum
{
    /**
     * Small tile (1x1).
     *
     * @var integer
     */
    const SMALL = 1;

    /**
     * Tall tile.
     *
     * @var integer
     */
    const TALL = 2;

    /**
     * Wide tile.
     *
     * @var integer
     */
    const WIDE = 3;

    /**
     * Number of grid cells occupied horizontally.
     */
    public $width;

    /**
     * Number of grid cells occupied vertically.
     */
    public $height;

    /**
     * Disallow creating instance using `new` and initialize height and width.
     */
    protected function __construct($value)
    {
        parent::__construct($value);
        $this->width = $value === static::WIDE ? 2 : 1;
        $this->height = $value === static::TALL ? 2 : 1;
    }
}
