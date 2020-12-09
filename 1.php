<?php

require __DIR__ . '/vendor/autoload.php';

// parse the input into an array of integers
$data = array_map(function ($item) {
    return (int)$item;
}, explode("\n", $data));

// nested loops to print all results (will print results multiple times!)
foreach ($data as $d1) {
    foreach ($data as $d2) {
        if ($d1 && $d2 && $d1 + $d2 === 2020) {
            answer('a', $d1 * $d2);
        }

        foreach ($data as $d3) {
            if ($d1 && $d2 && $d3 && $d1 + $d2 + $d3 === 2020) {
               answer('b', $d1 * $d2 * $d3);
            }
        }
    }
}
