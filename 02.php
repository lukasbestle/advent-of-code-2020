<?php

require __DIR__ . '/vendor/autoload.php';

use Kirby\Toolkit\Str;

// read the input into a line-based array
$data = explode("\n", $data);

$countA = $countB = 0;
foreach ($data as $line) {
    // split the line up without using a regex
    list($policy, $password) = explode(': ', $line);
    list($interval, $char)   = explode(' ', $policy);
    list($min, $max)         = explode('-', $interval);

    // first puzzle: The $char needs to be in $password $min-$max times
    $strCount = substr_count($password, $char);
    if ($strCount >= $min && $strCount <= $max) {
        $countA++;
    }

    // second puzzle: The $char needs to be at the $min XOR $max position
    $position1 = $password[$min - 1];
    $position2 = $password[$max - 1];
    if ($position1 === $char ^ $position2 === $char) {
        $countB++;
    }
}

answer('a', $countA);
answer('b', $countB);
