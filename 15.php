<?php

require __DIR__ . '/vendor/autoload.php';

// we need MOAR memory
ini_set('memory_limit', '256M');

// the input is one line of CSV
$data = array_map('intval', explode(',', $data));

// print the final (= 2020th or 30000000th) number
answer('a', simulate($data, 2020));
answer('b', simulate($data, 30000000));

/**
 * Simulates the game
 * 
 * @param array $data List of starting numbers
 * @param int $iterations Number of iterations to simulate
 * @return int The number of the $iterations iteration
 */
function simulate(array $data, int $iterations): int
{
    $history    = [];
    $lastNumber = null;
    for ($turn = 1; $turn <= $iterations; $turn++) {
        if (isset($data[$turn - 1]) === true) {
            // we can populate the history from the input
            // (which is zero-indexed!)
            $number = $data[$turn - 1];
        } elseif (isset($history[$lastNumber]) === true) {
            // the number was already spoken;
            // calculate the difference between the last turn number
            // and the turn number where the number was last spoken
            $number = $turn - 1 - $history[$lastNumber];
        } else {
            // the number wasn't spoken yet, always speak 0
            $number = 0;
        }

        // use the last turn for future lookups
        $history[$lastNumber] = $turn - 1;
        $lastNumber = $number;
    }

    return $number;
}
