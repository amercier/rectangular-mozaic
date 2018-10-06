<?php

namespace RectangularMozaic\Tests;

use PHPUnit\Framework\TestCase;
use RectangularMozaic\Cell;
use RectangularMozaic\Tile;

class TileTest extends TestCase
{
    public function testAllTilesAreDefined()
    {
        $this->assertInstanceOf(Tile::class, Tile::SMALL());
        $this->assertInstanceOf(Tile::class, Tile::TALL());
        $this->assertInstanceOf(Tile::class, Tile::WIDE());
    }

    public function testEachTileIsUnique()
    {
        foreach (Tile::toArray() as $name1 => $tile1) {
            foreach (Tile::toArray() as $name2 => $tile2) {
                if ($name1 !== $name2) {
                    $this->assertFalse($tile1 == $tile2);
                }
            }
        }
    }

    public function testWidthsAreCorrect()
    {
        $this->assertEquals(1, Tile::SMALL()->width);
        $this->assertEquals(1, Tile::TALL()->width);
        $this->assertEquals(2, Tile::WIDE()->width);
    }

    public function testHeightAreCorrect()
    {
        $this->assertEquals(1, Tile::SMALL()->height);
        $this->assertEquals(2, Tile::TALL()->height);
        $this->assertEquals(1, Tile::WIDE()->height);
    }

    public function testFromCellReturnsNullWhenNullIsGiven()
    {
        $this->assertSame(null, Tile::fromCell(null));
    }

    public function testFromCellReturnsTileWhenStartCellIsGiven()
    {
        $this->assertEquals(Tile::SMALL(), Tile::fromCell(Cell::SMALL()));
        $this->assertEquals(Tile::TALL(), Tile::fromCell(Cell::TALL_TOP()));
        $this->assertEquals(Tile::WIDE(), Tile::fromCell(Cell::WIDE_LEFT()));
    }

    public function testFromCellReturnsFalseWhenEndCellIsGiven()
    {
        $this->assertEquals(false, Tile::fromCell(Cell::TALL_BOTTOM()));
        $this->assertEquals(false, Tile::fromCell(Cell::WIDE_RIGHT()));
    }
}
