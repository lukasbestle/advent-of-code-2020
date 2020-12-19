<?php

require __DIR__ . '/vendor/autoload.php';

// read the input into a line-based array
$data = explode("\n", $data);

$mask0 = $mask1 = 0;
$maskX = [];
$mem1 = $mem2 = [];
foreach ($data as $command) {
    if (preg_match('/^mask = (.*)$/', $command, $matches) === 1) {
        // make a mask where every 0 stays a 0, but every other place is a 1
        $mask0 = intval(str_replace('X', '1', $matches[1]), 2);

        // ...and a mask where every 1 stays a 1, but every other place is a 0
        $mask1 = intval(str_replace('X', '0', $matches[1]), 2);

        // ...and an array where every X becomes [0, 1] and every other place [null]
        $maskX = array_map(function ($position) {
            if ($position === 'X') {
                return [0, 1];
            } else {
                return [null];
            }
        }, str_split($matches[1]));
    } elseif (preg_match('/^mem\[(\d+)\] = (\d+)$/', $command, $matches) === 1) {
        // first puzzle: set the memory value, but change all bits to
        // zeros that are 0 in $mask0 and all bits to ones that are 1 in $mask1
        $mem1[$matches[1]] = $matches[2] & $mask0 | $mask1;

        // second puzzle: set all memory addresses that match the bitmask
        // rule 2: all ones in the mask are always set to one
        // also convert the int to a 36-bit string for further manipulation
        $address = sprintf("%'036b", $matches[1] | $mask1);

        // rule 3: all X positions are floating
        $addresses = array_map(function ($product) use ($address) {
            foreach ($product as $position => $modification) {
                if ($modification !== null) {
                    $address[$position] = $modification;
                }
            }

            return $address;
        }, cartesianProduct(...$maskX));

        // set all affected addresses
        foreach ($addresses as $address) {
            $mem2[intval($address, 2)] = intval($matches[2]);
        }
    } else {
        throw new Exception('Could not parse command: ' . $command);
    }
}

answer('a', array_sum($mem1));
answer('b', array_sum($mem2));

/**
 * Creates the cartesian product of multiple arrays
 * as an array that contains every combination of
 * the inputs
 *
 * @param array ...$inputs Multiple arrays with any type of values
 * @return array
 */
function cartesianProduct(array ...$inputs): array
{
    $result = [[]];
    foreach ($inputs as $key => $inputValues) {
        $newResult = [];

        // for every possible value of that input,
        // append the value to each existing value
        // in the result array
        foreach ($inputValues as $inputValue) {
            foreach ($result as $resultValue) {
                $newResult[] = $resultValue + [$key => $inputValue];
            }
        }

        // throw away the old result and continue
        // with the longer result array for the next input
        $result = $newResult;
    }

    return $result;
}
