<?php

namespace RectangularMozaic\Tests;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RectangularMozaic\Distributor;

class DistributorTest extends TestCase
{
    public function testDistributeFailsWhenColumnsIsTooLow()
    {
        $this->expectException(InvalidArgumentException::class);
        Distributor::distribute(4, 10, 0.33, 0.33);
    }

    public function assertDitributeGeneratesDistributionAndGridThatMatch($columns)
    {
        for ($tiles = 2 * $columns; $tiles <= 20 * $columns; $tiles += 1) {
            [$distribution, $grid] = Distributor::distribute($tiles, $columns, 0.33, 0.33);
            $this->assertEquals(
                $grid->rows * $grid->columns,
                $distribution->getCells(),
                "Could not generate Distribution for {$tiles} tiles in {$columns} columns."
            );
        }
    }

    public function testDitributeGeneratesDistributionAndGridThatMatch2Columns()
    {
        $this->assertDitributeGeneratesDistributionAndGridThatMatch(2);
    }

    public function testDitributeGeneratesDistributionAndGridThatMatch3Columns()
    {
        $this->assertDitributeGeneratesDistributionAndGridThatMatch(3);
    }

    public function testDitributeGeneratesDistributionAndGridThatMatch4Columns()
    {
        $this->assertDitributeGeneratesDistributionAndGridThatMatch(4);
    }

    public function testDitributeGeneratesDistributionAndGridThatMatch5Columns()
    {
        $this->assertDitributeGeneratesDistributionAndGridThatMatch(5);
    }

    public function testDitributeGeneratesDistributionAndGridThatMatch6Columns()
    {
        $this->assertDitributeGeneratesDistributionAndGridThatMatch(6);
    }

    public function testDitributeGeneratesDistributionAndGridThatMatch7Columns()
    {
        $this->assertDitributeGeneratesDistributionAndGridThatMatch(7);
    }

    public function testDitributeGeneratesDistributionAndGridThatMatch8Columns()
    {
        $this->assertDitributeGeneratesDistributionAndGridThatMatch(8);
    }

    public function testDistributeGeneratesDistributionThatMatchesTargetRates()
    {
        [$distribution] = Distributor::distribute(100, 4, 0, 0.6);
        $this->assertEquals(0, $distribution->tall);
        $this->assertEquals(60, $distribution->wide);

        [$distribution] = Distributor::distribute(100, 4, 0.1, 0.5);
        $this->assertEquals(10, $distribution->tall);
        $this->assertEquals(50, $distribution->wide);

        [$distribution] = Distributor::distribute(100, 4, 0.3, 0.3);
        $this->assertEquals(30, $distribution->tall);
        $this->assertEquals(30, $distribution->wide);

        [$distribution] = Distributor::distribute(100, 4, 0.5, 0.1);
        $this->assertEquals(50, $distribution->tall);
        $this->assertEquals(10, $distribution->wide);

        [$distribution] = Distributor::distribute(100, 4, 0.6, 0);
        $this->assertEquals(60, $distribution->tall);
        $this->assertEquals(0, $distribution->wide);
    }
}
