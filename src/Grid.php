<?php

namespace RectangularMozaic;

use InvalidArgumentException;

/**
 * Grid.
 */
class Grid
{
    /**
     * Number of rows.
     *
     * @var int
     */
    public $rows;

    /**
     * Number of columns.
     *
     * @var int
     */
    public $columns;

    /**
     * Number of small tiles.
     *
     * @var int
     */
    public $small;

    /**
     * Number of tall tiles.
     *
     * @var int
     */
    public $tall;

    /**
     * Number of wide tiles.
     *
     * @var int
     */
    public $wide;

    /**
     * Cell values.
     *
     * @var Cell?[][]
     */
    protected $cells;

    /**
     * Create a new Grid instance and initialize all cells to `null`.
     *
     * @param int $rows Number of rows
     * @param int $columns Number of column
     */
    public function __construct(int $rows, int $columns)
    {
        Assert::assertPositiveInteger($rows, 'rows');
        Assert::assertPositiveInteger($columns, 'columns');

        $this->small = 0;
        $this->tall = 0;
        $this->wide = 0;

        $this->rows = $rows;
        $this->columns = $columns;
        $this->cells = array($rows);

        for ($i = 0; $i < $rows; $i += 1) {
            $this->cells[$i] = array_fill(0, $columns, null);
        }
    }

    /**
     * Get a string representation of the grid.
     *
     * @return string A string representation of the grid.
     */
    public function __toString()
    {
        return "{$this->rows}x{$this->columns} [{$this->small},{$this->tall},{$this->wide}]";
    }

    /**
     * Get the value at a given row and column.
     *
     * @param int $row Row index.
     * @param int $column Column index.
     * @return Cell The value at [$row, $column].
     * @throws InvalidArgumentException If row or column are either too low (negative) or too high.
     */
    public function get(int $row, int $column)
    {
        $this->assertIndexesAreValid($row, $column);
        return $this->cells[$row][$column];
    }

    /**
     * Determine whether a cell is empty.
     *
     * @param int $row Row index.
     * @param int $column Column index.
     * @return boolean `true` is the cell at [$row, $column] is empty, `false` otherwise.
     */
    public function isEmpty(int $row, int $column)
    {
        $this->assertIndexesAreValid($row, $column);
        return $this->get($row, $column) === null;
    }

    /**
     * Determine whether all required cells for a given tile are empty at [$row, $column].
     *
     * @param int $row Row index.
     * @param int $column Column index.
     * @param Tile $tile The tile.
     * @return void
     */
    public function isEmptyForTile($row, $column, $tile)
    {
        for ($x = 0; $x < $tile->width; $x += 1) {
            for ($y = 0; $y < $tile->height; $y += 1) {
                if (!$this->isEmpty($row + $y, $column + $x)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Assert a given [$width, $height] rectangle fits within the grid at [$row, $column] position.
     *
     * @param int $row Row index.
     * @param int $column Column index.
     * @param int $height Height of the rectangle.
     * @param int $width Width of the rectangle.
     * @return void
     */
    protected function assertIndexesAreValid($row, $column, $height = 1, $width = 1)
    {
        Assert::assertIntegerBetween(0, $this->rows - $height, $row, 'row');
        Assert::assertIntegerBetween(0, $this->columns - $width, $column, 'column');
    }

    /**
     * Assert a given tile fits within the grid at [$row, $column] position.
     *
     * @param int $row Row index.
     * @param int $column Column index.
     * @param Tile $tile The tile.
     * @return void
     */
    protected function assertIndexesAreValidForTile($row, $column, Tile $tile)
    {
        return $this->assertIndexesAreValid($row, $column, $tile->height, $tile->width);
    }

    /**
     * Assert all required cells for a given tile are empty at [$row, $column].
     *
     * @param int $row Row index.
     * @param int $column Column index.
     * @param Tile $tile The tile.
     * @return void
     */
    protected function assertEmptyTileSlot($row, $column, Tile $tile)
    {
        // Check indexes
        $this->assertIndexesAreValidForTile($row, $column, $tile);

        // Check all cells are empty
        if (!$this->isEmptyForTile($row, $column, $tile)) {
            throw new InvalidArgumentException(
                "Cannot set tile {$tile} at [$row, $column]: not empty."
            );
        }
    }

    /**
     * Set a cell values at a given row and column for a given tile.
     *
     * @param int $row Row index.
     * @param int $column Column index.
     * @param Tile $tile Tile to set.
     * @return Grid This object to allow chaining.
     * @throws InvalidArgumentException If row or column are either too low (negative) or too high.
     * @throws InvalidArgumentException If the cell is not empty.
     * @return void
     */
    public function set(int $row, int $column, Tile $tile)
    {
        $this->assertEmptyTileSlot($row, $column, $tile);
        switch ($tile->getValue()) {
            case Tile::SMALL:
                $this->cells[$row][$column] = Cell::SMALL();
                $this->small += 1;
                break;
            case Tile::TALL:
                $this->cells[$row][$column] = Cell::TALL_TOP();
                $this->cells[$row + 1][$column] = Cell::TALL_BOTTOM();
                $this->tall += 1;
                break;
            case Tile::WIDE:
                $this->cells[$row][$column] = Cell::WIDE_LEFT();
                $this->cells[$row][$column + 1] = Cell::WIDE_RIGHT();
                $this->wide += 1;
                break;
        }
    }

    /**
     * Reset the grid to an empty state (ie. remove all cell values).
     *
     * @return void
     */
    public function empty()
    {
        if ($this->small > 0 || $this->tall > 0 || $this->wide > 0) {
            for ($i = 0; $i < $this->rows; $i += 1) {
                for ($j = 0; $j < $this->columns; $j += 1) {
                    $this->cells[$i][$j] = null;
                }
            }
            $this->small = 0;
            $this->tall = 0;
            $this->wide = 0;
        }
    }

    /**
     * Get a copy of the cells array.
     *
     * @param boolean $flatten Whether to flatten the two-dimensional array to one, or not.
     * @param function? $cellToValue A function that takes an instance of Cell and return any value,
     * or `false` to omit it from the flattened array. If `null` is given, returns the original value.
     * @return mixed[] A one-dimensional of values, when `$flatten` is `true`, or
     * @return mixed[][] A two-dimensional of values, when `$flatten` is `false`.
     */
    public function getData(bool $flatten = false, callable $cellToValue = null)
    {
        $copy = array();
        for ($row = 0; $row < $this->rows; $row++) {
            if (!$flatten) {
                $copy[$row] = array();
            }
            foreach ($this->cells[$row] as $cell) {
                $value = $cellToValue ? $cellToValue($cell) : $cell;
                if ($flatten && $value === false) {
                    // skip
                } elseif ($flatten) {
                    $copy[] = $value;
                } else {
                    $copy[$row][] = $value;
                }
            }
        }
        return $copy;
    }

    /**
     * Get a copy of the grid cells.
     *
     * @param boolean $flatten Whether to flatten the two-dimensional array to one, or not.
     * @param boolean $raw Whether to return Cell instances or cell values (int).
     * @return int[] A one-dimensional of Cell values, when `$raw` is `false` and `$flatten` is `true`, or
     * @return int[][] A two-dimensional of Cell values, when `$raw` is `false` and `$flatten` is `false`.
     * @return Cell[] A one-dimensional of Cell instances, when `$raw` is `true` and `$flatten` is `true`, or
     * @return Cell[][] A two-dimensional of Cell instances, when `$raw` is `true` and `$flatten` is `false`.
     */
    public function getCells(bool $flatten = false, bool $raw = false)
    {
        return $this->getData($flatten, $raw ? null : function ($cell) {
            return Cell::toValue($cell);
        });
    }

    /**
     * Get a copy of the grid cells as an array of tiles.
     *
     * @param boolean $flatten Whether to flatten the two-dimensional array to one, or not.
     * @param boolean $raw Whether to return Tile instances or tile values (int).
     * @return int[] A one-dimensional of Tile values, when `$raw` is `false` and `$flatten` is `true`, or
     * @return int[][] A two-dimensional of Tile values, when `$raw` is `false` and `$flatten` is `false`.
     * @return Tile[] A one-dimensional of Tile instances, when `$raw` is `true` and `$flatten` is `true`, or
     * @return Tile[][] A two-dimensional of Tile instances, when `$raw` is `true` and `$flatten` is `false`.
     */
    public function getTiles(bool $flatten = false, bool $raw = false)
    {
        return $this->getData(
            $flatten,
            $raw
                ? function ($cell) {
                    return Tile::fromCell($cell);
                } : function ($cell) {
                    return Cell::toValue(Tile::fromCell($cell));
                }
        );
    }
}
