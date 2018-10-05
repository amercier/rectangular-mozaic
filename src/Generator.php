<?php

namespace RectangularMozaic;

/**
 * Grid generator.
 */
class Generator
{
    /**
     * Default settings:
     * - `tallRate` (float): target distribution rate of tall cells, {@see Distributor::distribute()}
     * - `wideRate` (float): target distribution rate of wide cells, {@see Distributor::distribute()}
     * - `maxFillRetries` (int): maximum number of retries when filling the grid randomly, {@see RandomFiller::fill()}
     *
     * @var array
     */
    const DEFAULT_SETTINGS = [
        'tallRate' => 0.25,
        'wideRate' => 0.25,
        'maxFillRetries' => 100,
    ];

    /**
     * Generator settings.
     *
     * @var array
     * @see DEFAULT_SETTINGS
     */
    protected $settings;

    /**
     * Create a new instance of Generator.
     *
     * @param array $options Optional settings, {@see DEFAULT_SETTINGS}.
     */
    protected function __construct($options = array())
    {
        $this->settings = array_merge(array(), self::DEFAULT_SETTINGS, $options);
    }

    /**
     * Generate a Grid that contains a given number of columns.
     *
     * @param int $tiles Total number of tiles.
     * @param int $columns Number of columns of the grid.
     * @return Grid A new grid filled with Cell values.
     */
    protected function generateGrid($tiles, $columns)
    {
        [$distribution, $grid] = Distributor::distribute(
            $tiles,
            $columns,
            $this->settings['tallRate'],
            $this->settings['wideRate']
        );
        RandomFiller::fill($grid, $distribution, $this->settings['maxFillRetries']);
        return $grid;
    }

    /**
     * Generate a Grid that contains a given number of columns.
     *
     * @param int $tiles Total number of tiles.
     * @param int $columns Number of columns of the grid.
     * @param array $options Optional settings, {@see DEFAULT_SETTINGS}.
     *
     * @return Grid A new grid filled with Cell values.
     */
    public static function generate($tiles, $columns, $options = array())
    {
        return (new Generator($options))->generateGrid($tiles, $columns);
    }
}
