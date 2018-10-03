<?php

namespace RectangularMozaic;

use InvalidArgumentException;
use RuntimeException;

/**
 * Distribution creator.
 */
class Distributor
{
    /**
     * Create a Distribution that fits a given number of tiles within a grid.
     *
     * @param int $tiles Total number of tiles of the distribution.
     * @param int $columns Number of columns of the grid.
     * @param float $tallRateTarget Targetted number of tall tiles per number of total tiles.
     * @param float $wideRateTarget Targetted number of wide tiles per number of total tiles.
     * @return mixed[] An array containing an empty grid, and the created Distribution.
     * @throws InvalidArgumentException If the number of columns is too low to fit the number of tiles.
     */
    public static function distribute(int $tiles, int $columns, float $tallRateTarget, float $wideRateTarget)
    {
        // 1. Approximate the number of rows where all tiles would fit
        $tallTilesTarget = (int)round($tallRateTarget * $tiles);
        $wideTilesTarget = (int)round($wideRateTarget * $tiles);
        $smallTilesTarget = $tiles - $tallTilesTarget - $wideTilesTarget;

        $cellsTarget = 2 * $tallTilesTarget + 2 * $wideTilesTarget + $smallTilesTarget;
        $rows = (int)round($cellsTarget / $columns);

        // Check $columns is reasonable
        if ($columns >= $cellsTarget || $tallTilesTarget > 0 && $rows < 2) {
            throw new InvalidArgumentException("Cannot fill {$tiles} items in {$columns} columns");
        }

        // 2. Adjust number of tiles to fit exactly the number of available cells
        $distribution = Distribution::fromLargeTiles($tiles, $tallTilesTarget, $wideTilesTarget);
        while (($cells = $distribution->getCells()) !== $rows * $columns) {
            // A. Not enough cells => decrement the number of tall/wide tiles
            if ($cells > $rows * $columns) {
                $distribution->decrementLargeTiles();
            // B. Too many cells => increment the number of tall/wide tiles
            } else {
                $distribution->incrementLargeTiles();
            }
        }

        return [$distribution, new Grid($rows, $columns)];
    }
}
