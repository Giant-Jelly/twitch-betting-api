<?php

namespace App\Helper;

use App\Entity\Round;

class RoundHelper
{
    const ROUND_OPEN = 1;
    const ROUND_CLOSED = 2;
    const ROUND_BETWEEN = 3;
    const ROUND_IN_PROGRESS = 4;

    /**
     * Get the status of a round
     *
     * @param Round $round
     * @return int
     */
    public static function getRoundStatus(Round $round): int
    {
        if (!$round->getFinished() && $round->getOpen()) {
            return self::ROUND_OPEN;
        } elseif (!$round->getFinished() && !$round->getOpen()) {
            return self::ROUND_CLOSED;
        } elseif ($round->getFinished()) {
            return self::ROUND_BETWEEN;
        }

        return self::ROUND_IN_PROGRESS;
    }
}
