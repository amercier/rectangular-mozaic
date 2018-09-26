<?php

namespace RectangularMozaic\Tests;

use PHPUnit\Framework\TestCase;
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
}
