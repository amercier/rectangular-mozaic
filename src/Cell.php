<?php

namespace RectangularMozaic;

/**
 * Value of a cell within a grid.
 * - Each grid cell takes 1 1x1 slot within a grid;
 * - Each grid cell can can either:
 *   1. contain a small tile (1x1), or
 *   2. be part of a 2-cell-wide tile (1x2), or
 *   3. be part of a 2-cell-tall tile (2x1).
 */
class Cell extends Enum
{
    /**
     * Small tile (1x1).
     *
     * @var integer
     */
    const SMALL = 0;

    /**
     * Top cell of a 2-cell-wide tile (1x2) tile.
     *
     * @var integer
     */
    const TALL_TOP = 1;

    /**
     * Bottom cell of a 2-cell-wide tile (1x2) tile.
     *
     * @var integer
     */
    const TALL_BOTTOM = 2;

    /**
     * Left cell of a 2-cell-wide (2x1) tile.
     *
     * @var integer
     */
    const WIDE_LEFT = 3;

    /**
     * Right cell of a 2-cell-wide (2x1) tile.
     *
     * @var integer
     */
    const WIDE_RIGHT = 4;
}
