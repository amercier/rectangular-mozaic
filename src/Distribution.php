<?php

namespace RectangularMozaic;

use Exception;

/**
 * Distribution of each tile format within a grid.
 */
class Distribution
{
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
     * Initial number of tall tiles tiles per total tiles.
     *
     * @var float
     */
    protected $initialTallRate;

    /**
     * Initial number of wide tiles tiles per total tiles.
     *
     * @var float
     */
    protected $initialWideRate;

    /**
     * Create a new Distribution.
     *
     * @param int $small Number of small tiles.
     * @param int $tall Number of tall tiles.
     * @param int $wide Number of wide tiles.
     */
    public function __construct(int $small, int $tall, int $wide)
    {
        Assert::assertNonNegativeInteger($small, 'small');
        Assert::assertNonNegativeInteger($tall, 'tall');
        Assert::assertNonNegativeInteger($wide, 'wide');

        $this->small = $small;
        $this->tall = $tall;
        $this->wide = $wide;

        $this->initialWideRate = $this->getWideRate();
        $this->initialTallRate = $this->getTallRate();
    }

    /**
     * Get a string representation of the distribution.
     *
     * @return string A string representation of the distribution.
     */
    public function __toString()
    {
        return "[{$this->small},{$this->tall},{$this->wide}]";
    }

    /**
     * Total number of tiles.
     *
     * @return int The sum of small, tall, and wide tiles.
     */
    public function getTiles()
    {
        return $this->small + $this->tall + $this->wide;
    }

    /**
     * Total number of cells occupied.
     *
     * @return int The sum of all cells occupied by each tile.
     */
    public function getCells()
    {
        return $this->small + 2 * ($this->tall + $this->wide);
    }

    /**
     * Number of tall tiles tiles per total tiles.
     *
     * @var float
     */
    public function getTallRate()
    {
        return $this->tall / $this->getTiles();
    }

    /**
     * Number of wide tiles tiles per total tiles.
     *
     * @var float
     */
    public function getWideRate()
    {
        return $this->wide / $this->getTiles();
    }

    /**
     * Decrement the number of large tiles, and increment the number of small tiles.
     *
     * Choice between tall or large tile is made by the following rules:
     * - if either the number of tall or wide tiles is zero, decrement the other ones.
     * - otherwise, decrements the one that is the closest to its initial rate.
     * - except if both are equally as close to their original rate, pick randomly.
     *
     * This means calling this method multiple times will keep a ratio of wide:tall tiles as close
     * as possible from the initial ratio.
     *
     * @return void
     * @throws Exception if both the number of tall or wide tiles are zero.
     */
    public function decrementLargeTiles()
    {
        if ($this->wide === 0 && $this->tall === 0) {
            throw new Exception('Cannot decrement the number of either wide or tall tiles.');
        }

        // Determine whether to decrement number of wide or tall tiles
        if ($this->tall === 0) {
            $decrementWide = true;
        } elseif ($this->wide > 0) {
            $wideRateDelta = $this->initialWideRate - $this->getWideRate();
            $tallRateDelta = $this->initialTallRate - $this->getTallRate();
            $decrementWide = $wideRateDelta === $tallRateDelta
                ? rand(0, 1) === 0
                : $wideRateDelta < $tallRateDelta;
        } else {
            $decrementWide = false;
        }

        // Decrement it
        if ($decrementWide) {
            $this->wide -= 1;
        } else {
            $this->tall -= 1;
        }
        $this->small += 1;
    }

    /**
     * Increment the number of large tiles, and decrement the number of small tiles.
     *
     * Choice between tall or large tile is made is made by the following rules:
     * - decrements the one that is the closest to its initial rate.
     * - except if both are equally as close to their original rate, pick randomly.
     *
     * This means calling this method multiple times will keep a ratio of wide:tall tiles as close
     * as possible from the initial ratio.
     *
     * @return void
     * @throws Exception if number of small tiles is already zero.
     */
    public function incrementLargeTiles()
    {
        if ($this->small === 0) {
            throw new Exception('Cannot decrement the number of small tiles, already 0.');
        }

        // Determine whether to decrement number of wide or tall tiles
        $wideRateDelta = $this->getWideRate() - $this->initialWideRate;
        $tallRateDelta = $this->getTallRate() - $this->initialTallRate;
        $incrementWide = $wideRateDelta === $tallRateDelta
            ? rand(0, 1) === 0
            : $wideRateDelta < $tallRateDelta;

        // Increment it
        if ($incrementWide) {
            $this->wide += 1;
        } else {
            $this->tall += 1;
        }
        $this->small -= 1;
    }

    /**
     * Convenience method to create a new Distribution from a total number of tiles, and number of
     * tall and wide tiles.
     *
     * @param int $total Total
     * @param int $tall Number of tall tiles.
     * @param int $wide Number of wide tiles.
     * @return Distribution A new distribution.
     */
    public static function fromLargeTiles(int $total, int $tall, int $wide)
    {
        return new Distribution($total - $tall - $wide, $tall, $wide);
    }
}
