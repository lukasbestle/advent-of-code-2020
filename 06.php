<?php

require __DIR__ . '/vendor/autoload.php';

// read the input into a group-based array
$data = explode("\n\n", $data);

$countA = $countB = 0;
foreach ($data as $group) {
    // first puzzle: find all unique answers per group
    // make a long combined string and sort out duplicates
    $groupAnswers = str_replace("\n", '', $group);
    $countA += count(array_unique(str_split($groupAnswers)));

    // second puzzle: find all answers in all forms of the group
    // split each member's answers into an array and find the intersection
    $members = array_map('str_split', explode("\n", $group));
    if (count($members) > 1) {
        $countB += count(array_intersect(...$members));
    } else {
        $countB += count($members[0]);
    }
}

answer('a', $countA);
answer('b', $countB);
