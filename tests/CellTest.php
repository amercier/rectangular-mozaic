<?php

namespace RectangularMozaic\Tests;

use PHPUnit\Framework\TestCase;
use RectangularMozaic\Cell;

class CellTest extends TestCase
{
    public function testAllCellsAreDefined()
    {
        $this->assertInstanceOf(Cell::class, Cell::SMALL());
        $this->assertInstanceOf(Cell::class, Cell::TALL_TOP());
        $this->assertInstanceOf(Cell::class, Cell::TALL_BOTTOM());
        $this->assertInstanceOf(Cell::class, Cell::WIDE_LEFT());
        $this->assertInstanceOf(Cell::class, Cell::WIDE_RIGHT());
    }

    public function testEachCellIsUnique()
    {
        foreach (Cell::toArray() as $name1 => $tile1) {
            foreach (Cell::toArray() as $name2 => $tile2) {
                if ($name1 !== $name2) {
                    $this->assertFalse($tile1 == $tile2);
                }
            }
        }
    }
}
