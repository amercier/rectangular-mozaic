<?php
require __DIR__ . '/../vendor/autoload.php';

use \RectangularMozaic\Generator as Mozaic;
use \RectangularMozaic\Cell;

$tilesNumber = 20;
$columns = 5;

$grid = Mozaic::generate($tilesNumber, $columns);

echo '<table>';
$i = 1;

foreach ($grid->getCells() as $row) {
    echo '<tr>';
    foreach ($row as $cell) {
        switch ($cell) {
            case Cell::SMALL:
                echo '<td>' . $i . '</td>';
                $i += 1;
                break;
            case Cell::TALL_TOP:
                echo '<td rowspan="2">' . $i . '</td>';
                $i += 1;
                break;
            case Cell::TALL_BOTTOM:
                break; // do nothing
            case Cell::WIDE_LEFT:
                echo '<td colspan="2">' . $i . '</td>';
                $i += 1;
                break;
            case Cell::WIDE_RIGHT:
                break; // do nothing
        }
    }
    echo '</tr>';
}
echo '</table>';
