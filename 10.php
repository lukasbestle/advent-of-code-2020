<?php

require __DIR__ . '/vendor/autoload.php';

// read the input into a line-based array
// and parse every line as an integer
$data = array_map('intval', explode("\n", $data));
sort($data);

// first puzzle: calculate the jolt differences of the power adapters
$count1Diff  = 0;
$count3Diff  = 1; // the device itself can step up by 3 jolts
$lastAdapter = 0;
foreach ($data as $adapter) {
    switch ($adapter - $lastAdapter) {
        case 1:
            $count1Diff++;
            break;
        case 3:
            $count3Diff++;
            break;
    }

    $lastAdapter = $adapter;
}

answer('a', $count1Diff * $count3Diff);

// second puzzle: count the number of different arrangements

// make a second list that includes the outlet and the device
$completeData = [0, ...$data, $data[count($data) - 1] + 3];

$arrangements = 1; // basic arrangement: keep all adapters
for ($i = 0; $i < count($completeData); $i++) {
    $adapter = $completeData[$i];

    // look for a sequence of 1 differences
    // and count how long that sequence is
    $j = $i - 1;
    do {
        $j++;
        $secondLastAdapter = $completeData[$j];
        $lastAdapter       = $completeData[$j + 1] ?? null;
    } while($lastAdapter - $secondLastAdapter === 1);

    // the number of combinations for that sequence (adapters to remove)
    // depends on the length of the sequence (minus the last adapter of
    // the sequence that cannot be removed to ensure a valid joltage
    // difference to the next adapter)
    $length = $j - $i - 1;
    if ($length === 3) {
        // one of the 4 adapters needs to be there at all times,
        // otherwise the joltage difference gets > 3 jolts;
        // so the combination "all four gone" is not valid
        $arrangements *= pow(2, $length) - 1; 
    } elseif ($length > 0) {
        $arrangements *= pow(2, $length);
    }

    // skip the adapters we already checked above on the next loop iteration
    $i = $j;
}

answer('b', $arrangements);
