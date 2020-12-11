<?php

require __DIR__ . '/vendor/autoload.php';

use Kirby\Toolkit\Str;

// read the input into a line-based array
// and parse the instructions
$instructions = array_map(function ($instruction) {
    return [
        'name'  => Str::before($instruction, ' '),
        'value' => (int)Str::after($instruction, ' ')
    ];
}, explode("\n", $data));

// first puzzle: simulate one run of the infinite loop
answer('a', simulate($instructions, false));

// second puzzle: fix the infinite loop

// first try to find a nop instruction that will jump
// out of the program when converted to a jmp instruction
// (maybe we are lucky and won't have to simulate all variants)
foreach ($instructions as $id => $instruction) {
    if ($instruction['name'] === 'nop') {
        $jmpTarget = $id + $instruction['value'];

        if (isset($instructions[$jmpTarget]) === false) {
            // success, calculate the new answer with a jmp instruction
            $instructions[$id]['name'] = 'jmp';
            answer('b', simulate($instructions, true) ?? 'no solution');
            exit(0);
        }
    }
}

// otherwise try to flip jmp and nop instructions one by one
// by brute-force and check if that fixes the problem
foreach ($instructions as $id => $instruction) {
    if ($instruction['name'] !== 'acc') {
        $test = $instructions;
        $test[$id]['name'] = $instruction['name'] === 'nop' ? 'jmp' : 'nop';
        if ($answer = simulate($test, true)) {
            // success
            answer('b', $answer);
            exit(0);
        }
    }
}

answer('b', 'no solution');

/**
 * Simulates one execution of the loop or the complete
 * program if it doesn't loop and returns the total
 * accumulator value
 *
 * @param array $instructions
 * @param bool $expectTermination If `true`, loops are not excepted
 * @return int|null `null` if the code looped even though that was not expected
 */
function simulate(array $instructions, bool $expectTermination): ?int
{
    // current instruction line
    $id = 0;

    // all instruction lines that have been executed so far
    $executed = [];

    // accumulator value
    $acc = 0;

    // follow the instructions until we have looped or the boot code is done
    while (in_array($id, $executed) !== true && $id < count($instructions)) {
        $executed[] = $id;

        // follow the instruction
        $instruction = $instructions[$id];
        if ($instruction['name'] === 'jmp') {
            $id += $instruction['value'];
            continue;
        } elseif ($instruction['name'] === 'acc') {
            $acc += $instruction['value'];
        }

        // run the next instruction on the next cycle
        // (unless it was a jmp instruction, handled above)
        $id++;
    }

    // check for termination if requested
    if ($expectTermination === true && $id < count($instructions)) {
        return null;
    }

    return $acc;
}
