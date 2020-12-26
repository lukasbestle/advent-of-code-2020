<?php

require __DIR__ . '/vendor/autoload.php';

// read the input into an array of cup numbers
$data = array_map('intval', str_split($data));

// first puzzle: simulate 100 rounds with the given cups
//               and return all cups counted after the 1 cup
$cups = simulate($data, 9, 100);
$onePosition = array_search(1, $cups);
$cups = array_merge(array_slice($cups, $onePosition + 1), array_slice($cups, 0, $onePosition));
answer('a', implode('', $cups));

// second puzzle: simulate a million rounds with a million cups
//                and return the two cups after the 1 cup
$cups = simulate($data, 1000000, 1000000);
$onePosition = array_search(1, $cups);
answer('b', $cups[$onePosition + 1] * $cups[$onePosition + 2]);

/**
 * Simulates the game
 *
 * @param array $data List of cups
 * @param int $cups Number of cups (if greater than the count, the cups
 *                  will be filled up after the given list)
 * @param int $rounds Number of rounds to simulate
 * @return array List of cups at the end of the simulation
 */
function simulate(array $data, int $cups, int $rounds): array
{
    // fill the data to reach the requested number of cups
    if ($cups > count($data)) {
        array_push($data, ...range(count($data) + 1, $cups));
    } else {
        $cups = count($data);
    }

    for ($i = 0; $i < $rounds; $i++) {
        // remove the next three cups from the list
        $removed = array_splice($data, 1, 3);

        // find the number of the destination cup
        $currentCup     = $data[0];
        $destinationCup = $currentCup - 1;
        if ($destinationCup === 0) {
            $destinationCup = $cups;
        }
        while(in_array($destinationCup, $removed) === true) {
            if ($destinationCup === 1) {
                $destinationCup = $cups;
            } else {
                $destinationCup--;
            }
        }

        // insert the removed cups after the destination cup
        $destinationPosition = array_search($destinationCup, $data);
        array_splice($data, $destinationPosition + 1, 0, $removed);

        // rotate the data array clockwise by one cup
        $data   = array_slice($data, 1);
        $data[] = $currentCup;
    }

    return $data;
}
