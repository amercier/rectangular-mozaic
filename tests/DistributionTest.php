<?php

namespace RectangularMozaic\Tests;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RectangularMozaic\Distribution;

class DistributionTest extends TestCase
{
    public function testConstructDoesNotAcceptNegativeSmallTiles()
    {
        $this->expectException(InvalidArgumentException::class);
        $distribution = new Distribution(-1, 1, 2);
    }

    public function testConstructDoesNotAcceptNegativeTallTiles()
    {
        $this->expectException(InvalidArgumentException::class);
        $distribution = new Distribution(1, -1, 2);
    }

    public function testConstructDoesNotAcceptNegativeWideTiles()
    {
        $this->expectException(InvalidArgumentException::class);
        $distribution = new Distribution(1, 2, -1);
    }

    public function testConstructSavesSmallTiles()
    {
        $distribution = new Distribution(2, 3, 4);
        $this->assertEquals(2, $distribution->small);
    }

    public function testConstructSavesTallTiles()
    {
        $distribution = new Distribution(2, 3, 4);
        $this->assertEquals(3, $distribution->tall);
    }

    public function testConstructSavesWideTiles()
    {
        $distribution = new Distribution(2, 3, 4);
        $this->assertEquals(4, $distribution->wide);
    }

    public function testToStringReturnsAStringRepresentation()
    {
        $distribution = new Distribution(2, 3, 4);
        $this->assertEquals('[2,3,4]', "{$distribution}");
    }

    public function testGetTilesReturnsTheTotalNumberOfTiles()
    {
        $distribution = new Distribution(3, 5, 7);
        $this->assertEquals(15, $distribution->getTiles());
    }

    public function testGetCellsReturnsTheTotalNumberOfCells()
    {
        $distribution = new Distribution(3, 5, 7);
        $this->assertEquals(27, $distribution->getCells());
    }

    public function testDecrementLargeTilesFailsIfBothTallAndWideTilesAreZero()
    {
        $this->expectException(Exception::class);
        $distribution = new Distribution(3, 0, 0);
        $distribution->decrementLargeTiles();
    }

    public function testDecrementLargeTilesDecrementsWideTilesIfTallTilesAreZero()
    {
        $distribution = new Distribution(1, 0, 4);
        $distribution->decrementLargeTiles();
        $this->assertEquals(3, $distribution->wide);
        $this->assertEquals(2, $distribution->small);
    }

    public function testDecrementLargeTilesDecrementsTallTilesIfWideTilesAreZero()
    {
        $distribution = new Distribution(1, 4, 0);
        $distribution->decrementLargeTiles();
        $this->assertEquals(3, $distribution->tall);
        $this->assertEquals(2, $distribution->small);
    }

    public function testDecrementLargeTilesDecrementsWideTilesIfTallTilesHaveBeenDecremented()
    {
        $distribution = new Distribution(0, 4, 6);
        $distribution->tall -= 1; // 3
        $distribution->small += 1; // 1

        $distribution->decrementLargeTiles();
        $this->assertEquals(2, $distribution->small);
        $this->assertEquals(3, $distribution->tall);
        $this->assertEquals(5, $distribution->wide);
    }

    public function testDecrementLargeTilesDecrementsTallTilesIfWideTilesHaveBeenDecremented()
    {
        $distribution = new Distribution(0, 6, 4);
        $distribution->wide -= 1; // 3
        $distribution->small += 1; // 1

        $distribution->decrementLargeTiles();
        $this->assertEquals(2, $distribution->small);
        $this->assertEquals(5, $distribution->tall);
        $this->assertEquals(3, $distribution->wide);
    }

    public function testDecrementLargeTilesDecrementsRandomlyIfNoneHaveBeenDecrementedYet()
    {
        $retries = 100;
        $tallDecremented = false;
        $wideDecremented = false;
        for ($i = $retries; $i > 0 && (!$tallDecremented || !$wideDecremented); $i -= 1) {
            $distribution = new Distribution(0, 1, 1);
            $distribution->decrementLargeTiles();
            $tallDecremented = $tallDecremented || $distribution->tall === 0;
            $wideDecremented = $wideDecremented || $distribution->wide === 0;
        }
        $this->assertTrue($tallDecremented, "Tall tiles have not been decremented after {$retries} retries.");
        $this->assertTrue($wideDecremented, "Wide tiles have not been decremented after {$retries} retries.");
    }

    public function testDecrementLargeTilesFailsIfSmallTilesAreZero()
    {
        $this->expectException(Exception::class);
        $distribution = new Distribution(0, 1, 2);
        $distribution->incrementLargeTiles();
    }

    public function testIncrementLargeTilesIncrementsWideTilesIfTallTilesHaveBeenIncremented()
    {
        $distribution = new Distribution(2, 3, 4);
        $distribution->tall += 1;
        $distribution->small -= 1;

        $distribution->incrementLargeTiles();
        $this->assertEquals(5, $distribution->wide);
        $this->assertEquals(0, $distribution->small);
    }

    public function testIncrementLargeTilesIncrementsTallTilesIfWideTilesHaveBeenIncremented()
    {
        $distribution = new Distribution(2, 4, 3);
        $distribution->wide += 1;
        $distribution->small -= 1;

        $distribution->incrementLargeTiles();
        $this->assertEquals(5, $distribution->tall);
        $this->assertEquals(0, $distribution->small);
    }


    public function testIncrementLargeTilesIncrementsRandomlyIfNoneHaveBeenIncrementedYet()
    {
        $retries = 100;
        $tallIncremented = false;
        $wideIncremented = false;
        for ($i = $retries; $i > 0 && (!$tallIncremented || !$wideIncremented); $i -= 1) {
            $distribution = new Distribution(1, 0, 0);
            $distribution->incrementLargeTiles();
            $tallIncremented = $tallIncremented || $distribution->tall === 1;
            $wideIncremented = $wideIncremented || $distribution->wide === 1;
        }
        $this->assertTrue($tallIncremented, "Tall tiles have not been incremented after {$retries} retries.");
        $this->assertTrue($wideIncremented, "Wide tiles have not been incremented after {$retries} retries.");
    }

    public function testFromLargeTilesCreatesNewDistribution()
    {
        $distribution = Distribution::fromLargeTiles(6, 2, 3);
        $this->assertEquals(1, $distribution->small);
        $this->assertEquals(2, $distribution->tall);
        $this->assertEquals(3, $distribution->wide);
    }
}
