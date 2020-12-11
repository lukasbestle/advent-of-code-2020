<?php

require __DIR__ . '/vendor/autoload.php';

use Kirby\Toolkit\Str;

// read the input into a line-based array and
// then make it a two-dimensional array
$data = array_map('str_split', explode("\n", $data));

// count all occupied seats after each simulation
answer('a', simulate($data, 1));
answer('b', simulate($data, null));

/**
 * Simulates the seating behavior until it stabilizes and
 * returns the number of occupied seats in the final state
 *
 * @param array $seats
 * @param int|null $maxDistance Maximum distance from the seat to test
 * @return int
 */
function simulate(array $seats, ?int $maxDistance = 1): int
{
    $newSeats = $seats;

    // simulate until the seat layout didn't change
    // compared to the last iteration
    do {
        $seats = $newSeats;

        foreach ($seats as $rowIndex => $row) {
            foreach ($row as $seatIndex => $seat) {
                if ($seat !== '.') {
                    $count = countVisiblePeople($seats, $rowIndex, $seatIndex, $maxDistance);

                    if ($seat === 'L' && $count === 0) {
                        // empty seats will be occupied if
                        // all adjacent seats are empty
                        $newSeats[$rowIndex][$seatIndex] = '#';
                    } elseif ($seat === '#' && $count >= ($maxDistance === null ? 5 : 4)) {
                        // occupied seats will be left if
                        // four/five or more adjacent seats are occupied
                        $newSeats[$rowIndex][$seatIndex] = 'L';
                    }
                }
            }
        }
    } while ($newSeats !== $seats);

    // count the number of occupied seats
    return array_reduce($seats, function ($count, $row) {
        return $count + count(array_filter($row, function ($seat) {
            return $seat === '#';
        }));
    }, 0);
}

/**
 * Counts the occupied seats that are directly visible
 * from the given seat
 *
 * @param array $seats
 * @param int $row Row index
 * @param int $seat Seat index
 * @param int|null $maxDistance Maximum distance from the seat to test
 * @return int Number from 0-8
 */
function countVisiblePeople(array $seats, int $row, int $seat, ?int $maxDistance = 1): int
{
    // set of all ray directions
    $directions = [
        [-1, -1],
        [-1, 0],
        [-1, 1],
        [0, -1],
        [0, 1],
        [1, -1],
        [1, 0],
        [1, 1],
    ];

    $count = 0;
    foreach ($directions as $direction) {
        $y = $row + $direction[0];
        $x = $seat + $direction[1];

        // follow the ray until we are outside the grid
        // or we have hit our distance limit
        $distance = 0;
        while (
            isset($seats[$y][$x]) === true &&
            (!$maxDistance || $distance < $maxDistance)
        ) {
            switch ($seats[$y][$x]) {
                case '#':
                    // the visible seat is occupied
                    $count++;

                    // no break, use the one below
                case 'L':
                    // empty seat, the seats behind it are not visible
                    break(2);
            }

            $y += $direction[0];
            $x += $direction[1];
            $distance++;
        }
    }

    return $count;
}
