<?php

require __DIR__ . '/vendor/autoload.php';

// read the input into a line-based array
$data = explode("\n", $data);

$rules = [
    'N' => ['rule' => 'y', 'amount' => 1],
    'S' => ['rule' => 'y', 'amount' => -1],
    'E' => ['rule' => 'x', 'amount' => 1],
    'W' => ['rule' => 'x', 'amount' => -1],
    'L' => ['rule' => 'rotate', 'amount' => -1],
    'R' => ['rule' => 'rotate', 'amount' => 1],
    'F' => ['rule' => 'forward']
];

$x1 = $x2 = 0;               // absolute X position of the ship (positive = east)
$y1 = $y2 = 0;               // absolute Y position of the ship (positive = north)
$waypointX = 10;             // relative X position of the waypoint (positive = east)
$waypointY = 1;              // relative Y position of the waypoint (positive = north)
$orientation1 = deg2rad(90); // ship orientation in radians (0rad = north)

foreach ($data as $line) {
    // split each line: first char = action, rest = value
    // e.g. L180
    $action = substr($line, 0, 1);
    $value  = (int)substr($line, 1);

    $rule = $rules[$action] ?? null;
    if (!$rule) {
        throw new Exception('Invalid rule ' . $action);
    }

    switch ($rule['rule']) {
        case 'x':
            $x1        += $rule['amount'] * $value;
            $waypointX += $rule['amount'] * $value;
            break;
        case 'y':
            $y1        += $rule['amount'] * $value;
            $waypointY += $rule['amount'] * $value;
            break;
        case 'rotate':
            // first puzzle: rotate the ship
            $orientation1 += $rotation = deg2rad($rule['amount'] * $value);

            // second puzzle: rotate the waypoint
            $newX      = $waypointX * cos($rotation) + $waypointY * sin($rotation);
            $waypointY = $waypointY * cos($rotation) - $waypointX * sin($rotation);
            $waypointX = $newX;
            break;
        case 'forward':
            // first puzzle: move the ship itself forward in its current orientation
            $x1 += sin($orientation1) * $value;
            $y1 += cos($orientation1) * $value;

            // second puzzle: move the ship in the direction of the waypoint
            $x2 += $waypointX * $value;
            $y2 += $waypointY * $value;
    }
}

answer('a', abs($x1) + abs($y1));
answer('b', abs($x2) + abs($y2));
