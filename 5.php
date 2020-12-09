<?php

require __DIR__ . '/vendor/autoload.php';

use Kirby\Toolkit\Str;

// read the input into a line-based array
$data = explode("\n", $data);

$ids = [];
$idMax = 0;
foreach ($data as $line) {
    // determine the row: walk through the first seven chars
    $rowMin = 0;
    $rowMax = 127;
    for ($i = 0; $i <= 6; $i++) {
        if ($line[$i] === 'F') {
            $rowMax -= ($rowMax - $rowMin + 1) / 2;
        } else {
            $rowMin += ($rowMax - $rowMin + 1) / 2;
        }
    }

    // determine the column: walk through the following three chars
    $colMin = 0;
    $colMax = 7;
    for ($i = 7; $i <= 9; $i++) {
        if ($line[$i] === 'L') {
            $colMax -= ($colMax - $colMin + 1) / 2;
        } else {
            $colMin += ($colMax - $colMin + 1) / 2;
        }
    }

    // calculate the seat ID;
    // the minimum and maximum row/column is now guaranteed to be the same,
    // so it doesn't matter which one to use
    $id = $rowMin * 8 + $colMin;

    $ids[] = $id;
    $idMax = max($id, $idMax);
}

answer('a', $idMax);

sort($ids);
foreach ($ids as $key => $id) {
    // if the next seat ID does not immediately follow,
    // the following ID is our seat
    if ($ids[$key + 1] !== $id + 1) {
        answer('b', $id + 1);
        break;
    }
}
