<?php

namespace RectangularMozaic\Tests;

use InvalidArgumentException;
use TypeError;
use PHPUnit\Framework\TestCase;
use RectangularMozaic\Cell;
use RectangularMozaic\Grid;
use RectangularMozaic\Tile;

class GridTest extends TestCase
{
    public function testConstructDoesNotAcceptZeroRows()
    {
        $this->expectException(InvalidArgumentException::class);
        new Grid(0, 3);
    }

    public function testConstructDoesNotAcceptZeroColumns()
    {
        $this->expectException(InvalidArgumentException::class);
        new Grid(2, 0);
    }

    public function testConstructDoesNotAcceptNegativeRows()
    {
        $this->expectException(InvalidArgumentException::class);
        new Grid(-1, 3);
    }

    public function testConstructDoesNotAcceptNegativeColumns()
    {
        $this->expectException(InvalidArgumentException::class);
        new Grid(2, -1);
    }

    public function testConstructSetsRows()
    {
        $grid = new Grid(2, 3);
        $this->assertEquals(2, $grid->rows);
    }

    public function testConstructSetsColumns()
    {
        $grid = new Grid(2, 3);
        $this->assertEquals(3, $grid->columns);
    }

    public function testConstructsInitializesAllCellsToNull()
    {
        $grid = new Grid(2, 3);
        for ($row = 0; $row < 2; $row++) {
            for ($column = 0; $column < 3; $column++) {
                $this->assertEquals(
                    null,
                    $grid->get($row, $column),
                    "Cell [{$row},{$column}] is not null: {$grid->get($row, $column)}"
                );
            }
        }
    }

    public function testSetDoesNotAcceptNullValue()
    {
        $this->expectException(TypeError::class);
        $grid = new Grid(2, 3);
        $grid->set(0, 0, null);
    }

    public function testSetDoesNotAcceptNegativeRow()
    {
        $this->expectException(InvalidArgumentException::class);
        $grid = new Grid(2, 3);
        $grid->set(-1, 0, Tile::SMALL());
    }

    public function testSetDoesNotAcceptNegativeColumn()
    {
        $this->expectException(InvalidArgumentException::class);
        $grid = new Grid(2, 3);
        $grid->set(0, -1, Tile::SMALL());
    }

    public function testSetDoesNotAcceptRowTooBig()
    {
        $this->expectException(InvalidArgumentException::class);
        $grid = new Grid(2, 3);
        $grid->set(2, 0, Tile::SMALL());
    }

    public function testSetDoesNotAcceptColumnOutsideGrid()
    {
        $this->expectException(InvalidArgumentException::class);
        $grid = new Grid(2, 3);
        $grid->set(0, 3, Tile::SMALL());
    }

    public function testSetDoesNotAcceptTallTileOutsideGrid()
    {
        $this->expectException(InvalidArgumentException::class);
        $grid = new Grid(2, 3);
        $grid->set(1, 0, Tile::TALL());
    }

    public function testSetDoesNotAcceptWideTileOutsideGrid()
    {
        $this->expectException(InvalidArgumentException::class);
        $grid = new Grid(2, 3);
        $grid->set(0, 2, Tile::WIDE());
    }

    public function testSetDoesNotAcceptTileOnCellOccupiedBySmallTile()
    {
        $this->expectException(InvalidArgumentException::class);
        $grid = new Grid(2, 3);
        $grid->set(1, 2, Tile::SMALL());
        $grid->set(1, 2, Tile::SMALL());
    }

    public function testSetDoesNotAcceptTileOnCellOccupiedByTallTile()
    {
        $this->expectException(InvalidArgumentException::class);
        $grid = new Grid(2, 3);
        $grid->set(0, 2, Tile::TALL());
        $grid->set(1, 2, Tile::SMALL());
    }

    public function testSetDoesNotAcceptTileOnCellOccupiedByWideTile()
    {
        $this->expectException(InvalidArgumentException::class);
        $grid = new Grid(2, 3);
        $grid->set(1, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());
    }

    public function testSetSetsSmallTileCell()
    {
        $grid = new Grid(2, 3);
        $grid->set(1, 2, Tile::SMALL());
        $this->assertEquals(Cell::SMALL(), $grid->get(1, 2));
    }

    public function testSetIncrementsSmall()
    {
        $grid = new Grid(2, 3);
        $this->assertEquals(0, $grid->small);
        $grid->set(0, 0, Tile::SMALL());
        $this->assertEquals(1, $grid->small);
        $grid->set(0, 1, Tile::SMALL());
        $this->assertEquals(2, $grid->small);
    }

    public function testSetSetsTallTileCells()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 2, Tile::TALL());
        $this->assertEquals(Cell::TALL_TOP(), $grid->get(0, 2));
        $this->assertEquals(Cell::TALL_BOTTOM(), $grid->get(1, 2));
    }

    public function testSetIncrementsTall()
    {
        $grid = new Grid(2, 3);
        $this->assertEquals(0, $grid->tall);
        $grid->set(0, 0, Tile::TALL());
        $this->assertEquals(1, $grid->tall);
        $grid->set(0, 1, Tile::TALL());
        $this->assertEquals(2, $grid->tall);
    }

    public function testSetSetsWideTileCells()
    {
        $grid = new Grid(2, 3);
        $grid->set(1, 1, Tile::WIDE());
        $this->assertEquals(Cell::WIDE_LEFT(), $grid->get(1, 1));
        $this->assertEquals(Cell::WIDE_RIGHT(), $grid->get(1, 2));
    }

    public function testSetIncrementsWide()
    {
        $grid = new Grid(2, 3);
        $this->assertEquals(0, $grid->wide);
        $grid->set(0, 0, Tile::WIDE());
        $this->assertEquals(1, $grid->wide);
        $grid->set(1, 0, Tile::WIDE());
        $this->assertEquals(2, $grid->wide);
    }

    public function testSetSetsAllTileCells()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());

        $this->assertEquals(Cell::TALL_TOP(), $grid->get(0, 0));
        $this->assertEquals(Cell::TALL_BOTTOM(), $grid->get(1, 0));
        $this->assertEquals(Cell::WIDE_LEFT(), $grid->get(0, 1));
        $this->assertEquals(Cell::WIDE_RIGHT(), $grid->get(0, 2));
        $this->assertEquals(null, $grid->get(1, 1));
        $this->assertEquals(Cell::SMALL(), $grid->get(1, 2));
    }

    public function testEmptySetsAllCellsToNull()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());
        $grid->empty();
        for ($row = 0; $row < 2; $row += 1) {
            for ($column = 0; $column < 3; $column += 1) {
                $this->assertEquals(
                    null,
                    $grid->get($row, $column),
                    "Cell [{$row},{$column}] is not empty: {$grid->get($row, $column)}"
                );
            }
        }
    }

    public function testEmptySetsCellNumbersToZero()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());
        $grid->empty();
        $this->assertEquals(0, $grid->small);
        $this->assertEquals(0, $grid->tall);
        $this->assertEquals(0, $grid->wide);
    }

    public function testToStringReturnsAStringRepresentation()
    {
        $grid = new Grid(3, 5);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::TALL());
        $grid->set(0, 2, Tile::WIDE());
        $grid->set(1, 2, Tile::WIDE());
        $grid->set(2, 2, Tile::WIDE());
        $grid->set(0, 4, Tile::SMALL());

        $this->assertEquals('3x5 [1,2,3]', "{$grid}");
    }

    public function testGetCellsDoesNotReturnTheOriginalArray()
    {
        $grid = new Grid(2, 3);
        $grid->set(1, 2, Tile::SMALL());

        $cells = $grid->getCells(false, true);
        $cells[1][2] = null;
        $this->assertEquals(Cell::SMALL(), $grid->get(1, 2));
    }

    public function testGetCellsReturnsTwoDimensionalArrayOfValues()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());

        $values = $grid->getCells();
        $this->assertSame(count($values), 2);
        $this->assertSame(count($values[0]), 3);
        $this->assertSame(count($values[1]), 3);
        $this->assertSame(Cell::TALL_TOP, $values[0][0]);
        $this->assertSame(Cell::WIDE_LEFT, $values[0][1]);
        $this->assertSame(Cell::WIDE_RIGHT, $values[0][2]);
        $this->assertSame(Cell::TALL_BOTTOM, $values[1][0]);
        $this->assertSame(null, $values[1][1]);
        $this->assertSame(Cell::SMALL, $values[1][2]);
    }

    public function testGetCellsReturnsOneDimensionalArrayOfValues()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());

        $values = $grid->getCells(true);
        $this->assertSame(count($values), 2 * 3);
        $this->assertSame(Cell::TALL_TOP, $values[0]);
        $this->assertSame(Cell::WIDE_LEFT, $values[1]);
        $this->assertSame(Cell::WIDE_RIGHT, $values[2]);
        $this->assertSame(Cell::TALL_BOTTOM, $values[3]);
        $this->assertSame(null, $values[4]);
        $this->assertSame(Cell::SMALL, $values[5]);
    }

    public function testGetCellsReturnsTwoDimensionalArrayOfCells()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());

        $cells = $grid->getCells(false, true);
        $this->assertSame(count($cells), 2);
        $this->assertSame(count($cells[0]), 3);
        $this->assertSame(count($cells[1]), 3);
        $this->assertEquals(Cell::TALL_TOP(), $cells[0][0]);
        $this->assertEquals(Cell::WIDE_LEFT(), $cells[0][1]);
        $this->assertEquals(Cell::WIDE_RIGHT(), $cells[0][2]);
        $this->assertEquals(Cell::TALL_BOTTOM(), $cells[1][0]);
        $this->assertEquals(null, $cells[1][1]);
        $this->assertEquals(Cell::SMALL(), $cells[1][2]);
    }

    public function testGetCellsReturnsOneDimensionalArrayOfCells()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());

        $cells = $grid->getCells(true, true);
        $this->assertSame(count($cells), 2 * 3);
        $this->assertEquals(Cell::TALL_TOP(), $cells[0]);
        $this->assertEquals(Cell::WIDE_LEFT(), $cells[1]);
        $this->assertEquals(Cell::WIDE_RIGHT(), $cells[2]);
        $this->assertEquals(Cell::TALL_BOTTOM(), $cells[3]);
        $this->assertEquals(null, $cells[4]);
        $this->assertEquals(Cell::SMALL(), $cells[5]);
    }

    public function testGetTilesReturnsTwoDimensionalArrayOfValues()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());

        $values = $grid->getTiles();
        $this->assertSame(count($values), 2);
        $this->assertSame(count($values[0]), 3);
        $this->assertSame(count($values[1]), 3);
        $this->assertSame(Tile::TALL, $values[0][0]);
        $this->assertSame(Tile::WIDE, $values[0][1]);
        $this->assertSame(false, $values[0][2]);
        $this->assertSame(false, $values[1][0]);
        $this->assertSame(null, $values[1][1]);
        $this->assertSame(Tile::SMALL, $values[1][2]);
    }

    public function testGetTilesReturnsOneDimensionalArrayOfValues()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());

        $values = $grid->getTiles(true);
        $this->assertSame(count($values), 4);
        $this->assertSame(Tile::TALL, $values[0]);
        $this->assertSame(Tile::WIDE, $values[1]);
        $this->assertSame(null, $values[2]);
        $this->assertSame(Tile::SMALL, $values[3]);
    }

    public function testGetTilesReturnsTwoDimensionalArrayOfTiles()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());

        $tiles = $grid->getTiles(false, true);
        $this->assertSame(count($tiles), 2);
        $this->assertSame(count($tiles[0]), 3);
        $this->assertSame(count($tiles[1]), 3);
        $this->assertEquals(Tile::TALL(), $tiles[0][0]);
        $this->assertEquals(Tile::WIDE(), $tiles[0][1]);
        $this->assertSame(false, $tiles[0][2]);
        $this->assertSame(false, $tiles[1][0]);
        $this->assertSame(null, $tiles[1][1]);
        $this->assertEquals(Tile::SMALL(), $tiles[1][2]);
    }

    public function testGetTilesReturnsOneDimensionalArrayOfTiles()
    {
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::TALL());
        $grid->set(0, 1, Tile::WIDE());
        $grid->set(1, 2, Tile::SMALL());

        $tiles = $grid->getTiles(true, true);
        $this->assertSame(count($tiles), 4);
        $this->assertEquals(Tile::TALL(), $tiles[0]);
        $this->assertEquals(Tile::WIDE(), $tiles[1]);
        $this->assertSame(null, $tiles[2]);
        $this->assertEquals(Tile::SMALL(), $tiles[3]);
    }
}
