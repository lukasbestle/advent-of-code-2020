<?php

require __DIR__ . '/vendor/autoload.php';

use Kirby\Toolkit\Str;

// read the input into a line-based array
$data = explode("\n", $data);

answer('a', calculateAll($data, 'calculateLeftToRight'));
answer('b', calculateAll($data, 'calculateAdditionPrecendence'));

/**
 * Calculates all expressions using the provided function
 * and returns the sum of all expressions
 *
 * @param array $data
 * @param string $function Name of the calculation function to call
 *                         on each expression string
 * @return int
 */
function calculateAll(array $data, string $function): int
{
    $total = 0;

    // parse and calculate each line and add the results to the total
    foreach ($data as $line) {
        // first get rid of all these parentheses from the inside out
        while (Str::contains($line, '(') === true) {
            $line = preg_replace_callback('/\(([^()]+)\)/', function ($matches) use ($function) {
                return $function($matches[1]);
            }, $line);
        }

        // now we have one linear line of operations, calculate that
        $total += $function($line);
    }

    return $total;
}

/**
 * Calculates an expression using left-to-right precendence logic
 *
 * @param string $expression
 * @return int
 */
function calculateLeftToRight(string $expression): int
{
    $values = explode(' ', $expression);

    // use the first operand as our base for calculation
    $result = array_shift($values);

    // now calculate using the remaining values
    $operator = null;
    foreach ($values as $i => $value) {
        if ($i % 2 === 0) {
            // an operator (+ or *)
            $operator = $value;
        } else {
            // an operand (a numeric value)
            switch ($operator) {
                case '+':
                    $result += $value;
                    break;
                case '*':
                    $result *= $value;
                    break;
                default:
                    throw new Exception('Invalid operator: ' . $operator);
            }
        }
    }

    return $result;
}

/**
 * Calculates an expression with precendence of addition
 * over multiplication
 *
 * @param string $expression
 * @return int
 */
function calculateAdditionPrecendence(string $expression): int
{
    // first replace all additions with their totals
    $expression = preg_replace_callback('/\d+( \+ \d+)+/', function ($matches) {
        return calculateLeftToRight($matches[0]);
    }, $expression);

    // then we can evalulate the rest left to right
    return calculateLeftToRight($expression);
}
