<?php

require __DIR__ . '/vendor/autoload.php';

// read the input into a passport-based array
$data = explode("\n\n", $data);

// first puzzle: simple existence check
$rulesA = [
    'byr' => ['required'],
    'iyr' => ['required'],
    'eyr' => ['required'],
    'hgt' => ['required'],
    'hcl' => ['required'],
    'ecl' => ['required'],
    'pid' => ['required'],
    'cid' => []
];

// second puzzle: actual validation
V::$validators['height'] = function ($value) {
    // validate the general format
    if (preg_match('/^([0-9]+)(cm|in)$/', $value, $matches) !== 1) {
        return false;
    }

    if ($matches[2] === 'cm') {
        return $matches[1] >= 150 && $matches[1] <= 193;
    } else {
        return $matches[1] >= 59 && $matches[1] <= 76;
    }
};
V::$validators['eyecolor'] = function ($value) {
    return in_array($value, ['amb', 'blu', 'brn', 'gry', 'grn', 'hzl', 'oth']);
};
V::$validators['strlen'] = function ($value, int $expected) {
    return strlen(trim($value)) === $expected;
};
$rulesB = [
    'byr' => ['required', 'strlen' => 4, 'min' => 1920, 'max' => 2002],
    'iyr' => ['required', 'strlen' => 4, 'min' => 2010, 'max' => 2020],
    'eyr' => ['required', 'strlen' => 4, 'min' => 2020, 'max' => 2030],
    'hgt' => ['required', 'height'],
    'hcl' => ['required', 'match' => '/#[0-9a-f]{6}/'],
    'ecl' => ['required', 'eyecolor'],
    'pid' => ['required', 'num', 'strlen' => 9],
    'cid' => []
];

$countA = $countB = 0;
foreach ($data as $passport) {
    // parse the passport into a key-value array
    $fieldsRaw = preg_split('/( |\n)/', $passport);
    $fields    = [];
    foreach ($fieldsRaw as $field) {
        $fields[Str::before($field, ':')] = Str::after($field, ':');
    }

    // check the data for validity
    if (invalid($fields, $rulesA) === []) {
        $countA++;
    }
    if (invalid($fields, $rulesB) === []) {
        $countB++;
    }
}

answer('a', $countA);
answer('b', $countB);
