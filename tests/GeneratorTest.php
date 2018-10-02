<?php

namespace RectangularMozaic\Tests;

use InvalidArgumentException;
use TypeError;
use PHPUnit\Framework\TestCase;
use RectangularMozaic\Generator;

class GeneratorTest extends TestCase
{
    public function assertRateMatch($expected, $actual, $maxDelta)
    {
        $delta = abs($actual - $expected);
        $this->assertTrue(
            $delta <= $maxDelta,
            "Actual rate doesn't match expected {$expected}: {$actual}. Delta: {$delta}"
        );
    }

    public function assertGenerateMatchesExpectedRates(
        $columns,
        $tallRate = Generator::DEFAULT_SETTINGS['tallRate'],
        $wideRate = Generator::DEFAULT_SETTINGS['wideRate']
    ) {
        $smallRate = 1.0 - $tallRate - $wideRate;
        $tiles = 10 * $columns;
        $grid = Generator::generate($tiles, $columns, [
            'tallRate' => $tallRate,
            'wideRate' => $wideRate,
        ]);
        $this->assertRateMatch($smallRate, $grid->small / $tiles, 0.05);
        $this->assertRateMatch($tallRate, $grid->tall / $tiles, 0.05);
        $this->assertRateMatch($wideRate, $grid->wide / $tiles, 0.05);
    }

    public function testGenerateSucceedsToGenerateA2ColumnsGridWithDefaultSettings()
    {
        $this->assertGenerateMatchesExpectedRates(2);
    }

    public function testGenerateSucceedsToGenerateA3ColumnsGridWithDefaultSettings()
    {
        $this->assertGenerateMatchesExpectedRates(3);
    }

    public function testGenerateSucceedsToGenerateA4ColumnsGridWithDefaultSettings()
    {
        $this->assertGenerateMatchesExpectedRates(4);
    }

    public function testGenerateSucceedsToGenerateA5ColumnsGridWithDefaultSettings()
    {
        $this->assertGenerateMatchesExpectedRates(5);
    }

    public function testGenerateSucceedsToGenerateA6ColumnsGridWithDefaultSettings()
    {
        $this->assertGenerateMatchesExpectedRates(6);
    }

    public function testGenerateSucceedsToGenerateA2ColumnsGridWithNoTallTiles()
    {
        $this->assertGenerateMatchesExpectedRates(2, 0, 0.5);
    }

    public function testGenerateSucceedsToGenerateA3ColumnsGridWithNoTallTiles()
    {
        $this->assertGenerateMatchesExpectedRates(3, 0, 0.5);
    }

    public function testGenerateSucceedsToGenerateA4ColumnsGridWithNoTallTiles()
    {
        $this->assertGenerateMatchesExpectedRates(4, 0, 0.5);
    }

    public function testGenerateSucceedsToGenerateA5ColumnsGridWithNoTallTiles()
    {
        $this->assertGenerateMatchesExpectedRates(5, 0, 0.5);
    }

    public function testGenerateSucceedsToGenerateA6ColumnsGridWithNoTallTiles()
    {
        $this->assertGenerateMatchesExpectedRates(6, 0, 0.5);
    }

    public function testGenerateSucceedsToGenerateA2ColumnsGridWithNoWideTiles()
    {
        $this->assertGenerateMatchesExpectedRates(2, 0.5, 0);
    }

    public function testGenerateSucceedsToGenerateA3ColumnsGridWithNoWideTiles()
    {
        $this->assertGenerateMatchesExpectedRates(3, 0.5, 0);
    }

    public function testGenerateSucceedsToGenerateA4ColumnsGridWithNoWideTiles()
    {
        $this->assertGenerateMatchesExpectedRates(4, 0.5, 0);
    }

    public function testGenerateSucceedsToGenerateA5ColumnsGridWithNoWideTiles()
    {
        $this->assertGenerateMatchesExpectedRates(5, 0.5, 0);
    }

    public function testGenerateSucceedsToGenerateA6ColumnsGridWithNoWideTiles()
    {
        $this->assertGenerateMatchesExpectedRates(6, 0.5, 0);
    }
}
