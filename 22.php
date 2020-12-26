<?php

require __DIR__ . '/vendor/autoload.php';

// parse the cards per player
$decks = array_map(function ($player) {
    // get the second to last line for each player
    $cards = array_slice(explode("\n", $player), 1);

    return array_map('intval', $cards);
}, explode("\n\n", $data));

// first puzzle: simulate the game linearly until any player has no cards anymore
[$deck1, $deck2] = $decks;
while (count($deck1) && count($deck2)) {
    $card1 = array_shift($deck1);
    $card2 = array_shift($deck2);

    if ($card1 > $card2) {
        // move the card of player 2 into player 1's deck
        $deck1[] = $card1;
        $deck1[] = $card2;
    } elseif ($card2 > $card1) {
        // move the card of player 1 into player 2's deck
        $deck2[] = $card2;
        $deck2[] = $card1;
    } else {
        throw new Exception('Card values are equal');
    }
}

answer('a', calculateScore($deck1, $deck2));

// second puzzle: simulate the game recursively
answer('b', calculateScore(...simulateRecursively(...$decks)));

/**
 * Calculates the score of the winning player
 *
 * @param array $deck1 Cards of player 1 at the end of the game
 * @param array $deck2 Cards of player 2 at the end of the game
 * @return int
 */
function calculateScore(array $deck1, array $deck2): int
{
    $winningDeck = array_merge($deck1, $deck2);

    $score = 0;
    foreach (array_reverse($winningDeck) as $i => $value) {
        $score += ($i + 1) * $value;
    }

    return $score;
}

/**
 * Simulates the game recursively
 *
 * @param array $deck1 Cards of player 1 at the beginning of the game
 * @param array $deck2 Cards of player 2 at the beginning of the game
 * @return array The two decks at the end of the game and the
 *               number of the winning player: [$deck1, $deck2, $winning]
 */
function simulateRecursively(array $deck1, array $deck2): array
{
    // cache of all decks that have occurred so far
    $rounds = [];

    $winning = null;
    while (count($deck1) && count($deck2)) {
        // rule 1: prevent infinite games
        $roundId = implode(',', $deck1) . '|' . implode(',', $deck2);
        if (in_array($roundId, $rounds) === true) {
            $winning = 1;
            break;
        }

        // rule 2: draw the first card from every deck
        $card1 = array_shift($deck1);
        $card2 = array_shift($deck2);

        // rule 3: is a recursive game possible?
        if (count($deck1) >= $card1 && count($deck2) >= $card2) {
            // yes, play a recursive game with the next x cards determined
            // by the number that was drawn from each deck
            $result = simulateRecursively(
                array_slice($deck1, 0, $card1),
                array_slice($deck2, 0, $card2)
            );

            $winningRound = $result[2];
        } else {
            // no, decide the round based on normal rules (rule 4)
            if ($card1 !== $card2) {
                $winningRound = $card1 > $card2 ? 1 : 2;
            } else {
                throw new Exception('Card values are equal');
            }
        }

        // move cards based on the round result
        if ($winningRound === 1) {
            // move the card of player 2 into player 1's deck
            $deck1[] = $card1;
            $deck1[] = $card2;
        } else {
            // move the card of player 1 into player 2's deck
            $deck2[] = $card2;
            $deck2[] = $card1;
        }

        // populate the result cache
        $rounds[] = $roundId;
    }

    // if the winner was not yet determined, determine
    // it based on the number of cards in the decks
    // (the loser has no cards anymore)
    if ($winning === null) {
        $winning = count($deck1) > 0 ? 1 : 2;
    }

    return [$deck1, $deck2, $winning];
}
