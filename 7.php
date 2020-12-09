<?php

require __DIR__ . '/vendor/autoload.php';

use Kirby\Toolkit\Str;

// read the input into a line-based array
$data = explode("\n", $data);

// parse the raw rules into a structured array
$rules = [];
foreach ($data as $rule) {
    // split the rule into the bag name and the contained bag types
    if (preg_match('/^(.*?) bags contain (.*).$/', $rule, $matches) !== 1) {
        throw new Exception('Could not parse rule: ' . $rule);
    }

    $bagName   = $matches[1];
    $contained = $matches[2];

    // always initialize the bag type, even if it doesn't contain anything
    $rules[$bagName] = [];

    if ($contained !== 'no other bags') {
        // parse the contained bag types into an array with their name and counts
        if (preg_match_all('/([0-9]+) (.*?) bags?/', $contained, $matches, PREG_SET_ORDER) === false) {
            throw new Exception('Could not parse contained bags: ' . $contained);
        }

        // convert that to an associative array: bag name => count
        foreach ($matches as $match) {
            $rules[$bagName][$match[2]] = (int)$match[1];
        }
    }
}

// first puzzle: count all top-level bags that contain at least one shiny gold bag
answer('a', count(
    array_filter($rules, function ($bagName) use ($rules) {
        return containsShinyGoldBag($rules, $bagName) === true;
    }, ARRAY_FILTER_USE_KEY)
));

// second puzzle: count the number of bags inside a shiny gold bag
answer('b', countContainedBags($rules, 'shiny gold'));

/**
 * Recursively checks if the given bag type contains
 * at least one shiny gold bag
 *
 * @param array $data
 * @param string $bagName Top-level bag name to check for
 * @return bool
 */
function containsShinyGoldBag(array $data, string $bagName): bool
{
    // loop through all bag types the given bag will contain
    foreach ($data[$bagName] as $contained => $containedCount) {
        // either we found a shiny gold bag directly or
        // we need to search recursively
        if (
            $contained === 'shiny gold' ||
            containsShinyGoldBag($data, $contained) === true
        ) {
            return true;
        }
    }

    // no contained bag may be or contain a shiny gold bag
    return false;
}

/**
 * Recursively counts the number of bags within the given
 * bag type
 *
 * @param array $data
 * @param string $bagName Top-level bag name to check for
 * @return bool
 */
function countContainedBags(array $data, string $bagName): int
{
    $count = 0;

    foreach ($data[$bagName] as $contained => $containedCount) {
        // the bag itself is contained
        $count += $containedCount;

        // the contained bag may also contain other bags
        $count += countContainedBags($data, $contained) * $containedCount;
    }

    return $count;
}
