<?php

namespace RectangularMozaic\Tests;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RectangularMozaic\Cell;
use RectangularMozaic\Distribution;
use RectangularMozaic\Grid;
use RectangularMozaic\RandomFiller;
use RectangularMozaic\Tile;

class RandomFillerTest extends TestCase
{
    public function testSetFailsWhenNoTallSlotIsAvailable()
    {
        $this->expectException(InvalidArgumentException::class);
        $grid = new Grid(2, 3);
        $grid->set(0, 0, Tile::SMALL());
        $grid->set(0, 1, Tile::SMALL());
        $grid->set(1, 2, Tile::SMALL());
        RandomFiller::set($grid, Tile::TALL());
    }

    public function testSetFailsWhenNoWideSlotIsAvailable()
    {
        $this->expectException(InvalidArgumentException::class);
        $grid = new Grid(3, 2);
        $grid->set(0, 0, Tile::SMALL());
        $grid->set(1, 0, Tile::SMALL());
        $grid->set(2, 1, Tile::SMALL());
        RandomFiller::set($grid, Tile::WIDE());
    }

    public function testFillFailsWhenGridDimensionsDontMatchDitribution()
    {
        $this->expectException(InvalidArgumentException::class);
        $distribution = new Distribution(8, 6, 5);
        $grid = new Grid(8, 4);
        RandomFiller::fill($grid, $distribution, 1);
    }

    public function assertFillFillsDistributionInGrid(Distribution $distribution, Grid $grid, int $tries)
    {
        RandomFiller::fill($grid, $distribution, $tries);
        $this->assertEquals($distribution->small, $grid->small);
        $this->assertEquals($distribution->tall, $grid->tall);
        $this->assertEquals($distribution->wide, $grid->wide);
    }

    public function assertFillFillsAlmostAnyDistribution(int $rows, int $columns, int $tries)
    {
        $maxTallTiles = $columns * (int)floor($rows / 2);
        for ($tallTiles = 0; $tallTiles <= $maxTallTiles; $tallTiles += 1) {
            $leftCells = $rows * $columns - 2 * $tallTiles;
            $maxWideTiles = min((int)floor($leftCells / 2), $rows * (int)floor($columns / 2));
            for ($wideTiles = 0; $wideTiles <= $maxWideTiles; $wideTiles += 1) {
                $smallTiles = $rows * $columns - 2 * ($tallTiles + $wideTiles);
                $distribution = new Distribution($smallTiles, $tallTiles, $wideTiles);
                if ($smallTiles > 1) {
                    $this->assertFillFillsDistributionInGrid($distribution, new Grid($rows, $columns), $tries);
                }
            }
        }
    }

    public function testFillCanFillAlmostAny3By3GridIn20Tries()
    {
        $this->assertFillFillsAlmostAnyDistribution(3, 3, 20);
    }

    public function testFillCanFillAlmostAny4By4GridIn50Tries()
    {
        $this->assertFillFillsAlmostAnyDistribution(4, 4, 50);
    }

    public function testFillCanFillAlmostAny5By5GridIn100Tries()
    {
        $this->assertFillFillsAlmostAnyDistribution(5, 5, 100);
    }

    public function testFillCanFillAlmostAny3By10GridIn100Tries()
    {
        $this->assertFillFillsAlmostAnyDistribution(3, 10, 100);
    }

    public function testFillCanFillAlmostAny10By3GridIn100Tries()
    {
        $this->assertFillFillsAlmostAnyDistribution(10, 3, 100);
    }

    public function testFillFillsTilesRandomly()
    {
        $rows = 5;
        $columns = 5;
        $distribution = new Distribution(5, 5, 5);
        $times = 100;

        // Initialize cell counts
        $counts = array($rows);
        for ($row = 0; $row < $rows; $row += 1) {
            $counts[$row] = array($columns);
            for ($column = 0; $column < $columns; $column += 1) {
                $counts[$row][$column] = [
                    Cell::SMALL => 0,
                    Cell::TALL_TOP => 0,
                    Cell::TALL_BOTTOM => 0,
                    Cell::WIDE_LEFT => 0,
                    Cell::WIDE_RIGHT => 0,
                ];
            }
        }

        // Fill a grid $times times, and add cell counts
        for ($i = 0; $i < $times; $i += 1) {
            $grid = new Grid($rows, $columns);
            RandomFiller::fill($grid, $distribution, 100);
            for ($row = 0; $row < $rows; $row += 1) {
                for ($column = 0; $column < $columns; $column += 1) {
                    $counts[$row][$column][$grid->get($row, $column)->getValue()] += 1;
                }
            }
        }

        // Check all cells appear at least once in each cell
        for ($r = 0; $r < $rows; $r += 1) {
            for ($c = 0; $c < $columns; $c += 1) {
                $this->assertNotEquals(0, $counts[$r][$c][Cell::SMALL], "No SMALL at [{$r},{$c}].");
                if ($r < $rows - 1) {
                    $this->assertNotEquals(0, $counts[$r][$c][Cell::TALL_TOP], "No TALL_TOP at [{$r},{$c}].");
                }
                if ($r > 0) {
                    $this->assertNotEquals(0, $counts[$r][$c][Cell::TALL_BOTTOM], "No TALL_BOTTOM at [{$r},{$c}].");
                }
                if ($c < $columns - 1) {
                    $this->assertNotEquals(0, $counts[$r][$c][Cell::WIDE_LEFT], "No WIDE_LEFT at [{$r},{$c}].");
                }
                if ($c > 0) {
                    $this->assertNotEquals(0, $counts[$r][$c][Cell::WIDE_RIGHT], "No WIDE_RIGHT at [{$r},{$c}].");
                }
            }
        }
    }
}
