<?php

require __DIR__ . '/vendor/autoload.php';

// we need MOAR memory
ini_set('memory_limit', '512M');

// split the input by section
[$rulesRaw, $messages] = explode("\n\n", $data);

// parse the rules into an array structure
$rules = [];
foreach (explode("\n", $rulesRaw) as $rule) {
    if (preg_match('/^(\d+): (?:"([ab])"|(\d+(?: (?:\| )?\d+)*))$/', $rule, $matches) !== 1) {
        throw new Exception('Could not parse rule: ' . $rule);
    }

    if ($matches[2]) {
        // a letter
        $rule = $matches[2];
    } else {
        // a recursive rule

        // split all options
        $options = explode('|', $matches[3]);

        // convert each option to a list of rule numbers
        $rule = array_map(function($option) {
            return array_map('intval', explode(' ', trim($option)));
        }, $options);
    }

    $rules[(int)$matches[1]] = $rule;
}

// first puzzle: count the messages that match rule 0
$count = 0;
foreach (explode("\n", $messages) as $message) {
    if (checkRule($rules, 0, $message) === true) {
        $count++;
    }
}

answer('a', $count);

/**
 * Checks if the given rule matches the given message
 *
 * @param array $rules List of rule grammars
 * @param int $rule ID of the rule inside the $rules array to check
 * @param string $message
 * @return bool
 */
function checkRule(array $rules, int $rule, string $message): bool
{
    // global cache
    static $ruleOptions = [];

    if (isset($ruleOptions[$rule]) !== true) {
        $ruleOptions[$rule] = ruleOptions($rules, $rule);
    }

    return in_array($message, $ruleOptions[$rule]);
}

/**
 * Recursively generates all possible options for the given rule
 *
 * @param array $rules List of rule grammars
 * @param int $rule ID of the rule inside the $rules array
 *                  to generate the options for
 * @return array
 */
function ruleOptions(array $rules, int $rule): array
{
    $rule = $rules[$rule] ?? null;

    if (is_string($rule) === true) {
        // a single char to check
        return [$rule];
    } elseif (is_array($rule) === true) {
        // a recursive rule with one or multiple possible options
        // recursively convert each option to an array of possible strings
        $options = array_map(function ($option) use ($rules) {
            // convert each contained rule to its own options
            $ruleOptions = array_map(function ($rule) use ($rules) {
                return ruleOptions($rules, $rule);
            }, $option);

            // assemble all possible variants as a cartesian product
            return cartesianProduct(...$ruleOptions);
        }, $rule);

        // merge the results into a linear array of possible strings
        return array_unique(array_merge(...$options));
    } else {
        throw new Exception('Invalid rule: ' . $rule);
    }
}

/**
 * Creates the cartesian product of multiple arrays
 * as an array that contains every combination of
 * the inputs
 *
 * @param array ...$inputs Multiple arrays each with string values
 * @return array
 */
function cartesianProduct(array ...$inputs): array
{
    $result = [''];
    foreach ($inputs as $inputValues) {
        $newResult = [];

        // for every possible value of that input,
        // append the value to each existing value
        // in the result array
        foreach ($inputValues as $inputValue) {
            foreach ($result as $resultValue) {
                $newResult[] = $resultValue . $inputValue;
            }
        }

        // throw away the old result and continue
        // with the longer result array for the next input
        $result = $newResult;
    }

    return $result;
}
