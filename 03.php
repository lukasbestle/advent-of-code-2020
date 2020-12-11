<?php

require __DIR__ . '/vendor/autoload.php';

use Kirby\Toolkit\Str;

// read the input into a line-based array
$data = explode("\n", $data);

answer('a', countTrees($data, 3, 1));
answer('b',
    countTrees($data, 1, 1) *
    countTrees($data, 3, 1) *
    countTrees($data, 5, 1) *
    countTrees($data, 7, 1) *
    countTrees($data, 1, 2)
);

/**
 * Counts the number of trees while walking through the grid
 *
 * @param array $data
 * @param int $deltaX Positions to go right on each step
 * @param int $deltaY Positions to go down on each step
 * @return int
 */
function countTrees(array $data, int $deltaX, int $deltaY): int
{
    // determine the size of the grid
    $width  = Str::length($data[0]);
    $height = count($data);

    // walk through the grid until we are at the bottom
    $count = 0;
    $posX = $posY = 0;
    while ($posY < $height) {
        if ($data[$posY][$posX] === '#') {
            $count++;
        }

        $posX += $deltaX;
        $posY += $deltaY;

        // if we are at the right, go back one width
        if ($posX + 1 > $width) {
            $posX -= $width;
        }
    }

    return $count;
}
