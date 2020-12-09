<?php

use Kirby\Toolkit\F;

// get the day number from the calling script
$GLOBALS['day'] = basename($_SERVER['argv'][0], '.php');

// load the data from the input file
$path = __DIR__ . '/inputs/' . $GLOBALS['day'] . '.txt';
if (is_file($path) !== true) {
    echo "\033[31mInput file \033[34m$path\033[31m is missing.\n";
    exit(1);
}
$GLOBALS['data'] = trim(F::read($path));

/**
 * Prints the answer to one part of a day
 *
 * @param string $part Either 'a' or 'b'
 * @param string $result
 * @return void
 */
function answer(string $part, string $result): void
{
    static $printed = [];

    // fade out answers that have already been printed
    $id = $GLOBALS['day'] . $part;
    if (in_array($id, $printed) === true) {
        echo "\033[2m";
    }

    echo "\033[1mAnswer $id:\033[0;34m $result\033[0m\n";

    $printed[] = $id;
}
