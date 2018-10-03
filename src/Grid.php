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
     * @param function $copyRow A function that takes an array of cells and returns an array of the same length.
     * @return mixed[][] A new two-dimensional array containing values.
     */
    protected function getCellsCopy($copyRow)
    {
        $copy = array();
        for ($i = 0; $i < $this->rows; $i++) {
            $copy[$i] = $copyRow($this->cells[$i]);
        }
        return $copy;
    }

    /**
     * Get a copy of the cells array.
     *
     * @return Cell?[][] A new two-dimensional array containing cells.
     */
    public function getCells()
    {
        return $this->getCellsCopy(function ($row) {
            return array_slice($row, 0);
        });
    }

    /**
     * Get a two-dimensional array containing cell values.
     *
     * @return int?[][] A new two-dimensional array containing cell values.
     */
    public function getValues()
    {
        return $this->getCellsCopy(function ($row) {
            $nullableCellToInt = function ($cell) {
                return $cell === null ? null : $cell->getValue();
            };
            return array_map($nullableCellToInt, $row);
        });
    }
}
