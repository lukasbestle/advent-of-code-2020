<?php

require __DIR__ . '/vendor/autoload.php';

// read the input into a line-based array
// and parse every line as an integer
$data = array_map('intval', explode("\n", $data));

// first puzzle: find the first number that doesn't validate
// loop through all numbers starting from the 26th
$invalidIndex = -1;
for ($i = 25; $i < count($data); $i++) {
    // now check all combinations of all 25 numbers before it
    for ($check1 = $i - 24; $check1 < $i; $check1++) {
        for ($check2 = $i - 25; $check2 < $check1; $check2++) {
            if (
                $data[$check1] !== $data[$check2] &&
                $data[$check1] + $data[$check2] === $data[$i]
            ) {
                // success, the number is valid
                // skip to the next iteration of the uppermost loop
                // (the next number to check)
                continue(3);
            }
        }
    }

    // no success, we found our first answer, so stop searching
    $invalidIndex = $i;
    break;
}

$invalidNumber = $data[$invalidIndex] ?? null;
if (!$invalidNumber) {
    answer('a', 'no solution');
    exit(1);
}
answer('a', $invalidNumber);

// second puzzle: find a contiguous range of numbers that add to that number
for ($i = 0; $i < $invalidIndex - 2; $i++) {
    // increase the count starting from $i until
    // we either found a solution or the count is too high
    $count = 2;
    do {
        // get the set of numbers starting at $i until $i + $count
        // and sum all elements of that set
        $slice = array_slice($data, $i, $count);
        $sum   = array_sum($slice);

        if ($sum === $invalidNumber) {
            // we found the solution, report the smallest and largest number of the set
            sort($slice);
            answer('b', $slice[0] + $slice[$count - 1]);
            exit(0);
        }

        // try the next largest set next time
        $count++;
    } while($sum < $invalidNumber);
}
