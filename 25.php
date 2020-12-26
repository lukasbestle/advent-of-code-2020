<?php

require __DIR__ . '/vendor/autoload.php';

// read the input into a line-based array
// (the two public keys)
$data = array_map('intval', explode("\n", $data));

// find the loop size behind the second public key
// via brute-force
$encryptionKey = null;
foreach (transformGenerator(7) as [$loopSize, $value]) {
    if ($value === $data[1]) {
        // we can now calculate the encryption key
        // with the first public key
        $encryptionKey = transform($data[0], $loopSize);
        break;
    }
}

// first puzzle: report the exchanged encryption key
answer('a', $encryptionKey);

/**
 * Transforms the given subject number
 *
 * @param int $subjectNumber
 * @param int $loopSize
 * @return int
 */
function transform(int $subjectNumber, int $loopSize): int
{
    $value = 1;

    for ($i = 0; $i < $loopSize; $i++) {
        $value *= $subjectNumber;
        $value = $value % 20201227;
    }

    return $value;
}

/**
 * Generator for number transformation with variable loop size
 *
 * @param int $subjectNumber
 * @return iterable Each item: [$loopSize, $value]
 */
function transformGenerator(int $subjectNumber): iterable
{
    $value = 1;

    for ($loopSize = 1; true; $loopSize++) {
        $value *= $subjectNumber;
        $value = $value % 20201227;

        yield [$loopSize, $value];
    }
}
