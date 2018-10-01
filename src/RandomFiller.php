<?php

namespace RectangularMozaic;

use RuntimeException;
use InvalidArgumentException;

/**
 * Utility function to fill a grid
 */
class RandomFiller
{
    /**
     * Set a tile randomly within a grid.
     *
     * @param Grid $grid Grid to fill the tile in.
     * @param Tile $tile Tile to set.
     * @return int[] Row and column where the tile was set.
     * @throws InvalidArgumentException If there is no spot available for the given tile in the grid.
     */
    public static function set(Grid $grid, Tile $tile)
    {
        // Boundaries of the grid within which the tile would fit
        $rows = $grid->rows - $tile->height + 1;
        $columns = $grid->columns - $tile->width + 1;

        // Start at a random position
        $row = rand(0, $rows - 1);
        $column = rand(0, $columns - 1);

        // Record the initial position to prevent infinite loop
        $initialRow = $row;
        $initialColumn = $column;

        // Iterate over grid until an empty spot is found
        while (!$grid->isEmptyForTile($row, $column, $tile)) {
            $column = ($column + 1) % $columns;
            if ($column === 0) {
                $row = ($row + 1) % $rows;
            }
            if ($row === $initialRow && $column === $initialColumn) {
                throw new InvalidArgumentException("No empty slot for {$tile} tile in grid: {$grid}");
            }
        }

        $grid->set($row, $column, $tile);
        return [$row, $column];
    }

    /**
     * Fill a tiles distribution randomly within a grid.
     *
     * @param Grid $grid Grid to fill.
     * @param Distribution $distribution Distribution to set.
     * @return void
     * @throws InvalidArgumentException If there is no spot available for the given tile in the grid.
     */
    public static function tryToFill(Grid $grid, Distribution $distribution)
    {
        $smallTiles = $distribution->small;
        $tallTiles = $distribution->tall;
        $wideTiles = $distribution->wide;

        for ($i = 0; $i < $distribution->getTiles(); $i++) {
            // Fill small tiles at the end
            if ($wideTiles === 0 && $tallTiles === 0) {
                static::set($grid, Tile::SMALL());
                $smallTiles -= 1;
            // Fill tall tiles if more or equal than wide ones
            } elseif ($tallTiles >= $wideTiles) {
                static::set($grid, Tile::TALL());
                $tallTiles -= 1;
            // Fill wide tiles if more than tall ones
            } else {
                static::set($grid, Tile::WIDE());
                $wideTiles -= 1;
            }
        }
    }

    /**
     * Try to fill a tiles distribution randomly within a grid.
     *
     * @param Grid $grid Grid to fill.
     * @param Distribution $distribution Distribution to set.
     * @param int $maxRetries Maximum number of tries.
     * @return void
     * @throws Exception If the grid could not be filled after $maxRetries retries.
     */
    public static function fill(Grid $grid, Distribution $distribution, int $maxRetries)
    {
        Assert::assertPositiveInteger($maxRetries, 'maxRetries');

        if ($distribution->getCells() !== ($cells = $grid->rows * $grid->columns)) {
            throw new InvalidArgumentException(
                "Cannot fill distribution {$distribution} ({$distribution->getCells()} cells)"
                . " in a {$grid->rows}x{$grid->columns} grid ({$cells} cells)"
            );
        }

        if ($grid->rows < 2 && $distribution->tall > 0) {
            throw new InvalidArgumentException(
                "Cannot fill distribution {$distribution} in a {$grid->rows}x{$grid->columns} grid: not enough rows."
            );
        }

        if ($grid->columns < 2 && $distribution->wide > 0) {
            throw new InvalidArgumentException(
                "Cannot fill distribution {$distribution} in a {$grid->rows}x{$grid->columns} grid: not enough columns."
            );
        }

        for ($remainingTries = $maxRetries; $remainingTries > 0; $remainingTries -= 1) {
            try {
                return static::tryToFill($grid, $distribution);
            } catch (InvalidArgumentException $e) {
                $grid->empty();
            }
        }
        throw new RuntimeException(
            "Could not fill distribution {$distribution} in a {$grid->rows}x{$grid->columns} grid"
            . " after {$maxRetries} tries."
        );
    }
}
