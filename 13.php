<?php

require __DIR__ . '/vendor/autoload.php';

use Kirby\Toolkit\Collection;

// read the input into the first line and
// a collection of the bus line numbers
$data = explode("\n", $data);
$departTime = (int)$data[0];
$busLines   = (new Collection(explode(',', $data[1])))->map(function ($line) {
    return new Obj([
        'isOutOfService' => $line === 'x',
        'number'         => (int)$line
    ]);
});

// first puzzle: find the earliest bus we can take
$firstLine = $busLines
    ->filterBy('isOutOfService', false)
    ->map(function ($line) use ($departTime) {
        // calculate the next departure based on the minimum departure time
        $line->nextDeparture = $line->number() * ceil($departTime / $line->number());

        return $line;
    })
    ->sortBy('nextDeparture', 'asc')
    ->first();

answer('a', $firstLine->number() * ($firstLine->nextDeparture() - $departTime));

// second puzzle: find the earliest timestamp where each bus line
//                will depart one minute after the previous

// make a more efficient subset of the list of bus lines
// (the first bus line only needs to be checked in the outer loop)
$busLines        = $busLines->filterBy('isOutOfService', false);
$firstLineNumber = $busLines->first()->number();
$busLines        = $busLines->not(0);

// now find a suitable timestamp to start the loop
// (greater than the magic number, but where the first bus can depart)
$magic = 100000000000000;
$i = gmp_init((int)ceil($magic / $firstLineNumber) * $firstLineNumber);

// check each timestamp where the first bus will depart
while (true) {
    // now check if every bus line fulfills the condition at the timestamp
    foreach ($busLines as $j => $line) {
        if (gmp_mod(gmp_add($i, $j), $line->number()) > 0) {
            // no, skip to the next timestamp
            $i = gmp_add($i, $firstLineNumber);
            continue(2);
        }
    }

    // all bus lines matched, so we found our solution
    answer('b', $i);
    exit(0);
}
