<?php

namespace App\Helper;

use App\Entity\Outcome;
use App\Entity\Round;

class OutcomeHelper
{
    /**
     * @param Round $round
     * @return string
     */
    public static function getOutcomeColour(Round $round): string
    {
        $colours = [];
        foreach ($round->getOutcomes() as $outcome) {
            $colours[] = $outcome->getColour();
        }

        $colour = Outcome::COLOURS[array_rand(Outcome::COLOURS)];
        if (in_array($colour, $colours)) {
            return self::getOutcomeColour($round);
        }

        return $colour;
    }

    /**
     * @param Outcome $outcome
     * @return float
     */
    public static function getAdjustedPayout(Outcome $outcome):float
    {
        if ($outcome->getWon()) {
            return $outcome->getPayout() - 0.02;
        }
        return $outcome->getPayout() + 0.02;
    }
}
