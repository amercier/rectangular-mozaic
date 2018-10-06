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

    /**
     * Create the Tile corresponding to a cell.
     *
     * @param Cell? $cell A cell, or `null`.
     * @return null If `null` is given.
     * @return false If the `$cell` is `WIDE_LEFT` or `TALL_BOTTOM`, or
     * @return Tile The Tile corresponding to the Cell value.
     */
    public static function fromCell($cell)
    {
        switch (Cell::toValue($cell)) {
            case null:
                return null;
            case Cell::SMALL:
                return self::SMALL();
            case Cell::TALL_TOP:
                return self::TALL();
            case Cell::WIDE_LEFT:
                return self::WIDE();
            default:
                return false;
        }
    }
}
