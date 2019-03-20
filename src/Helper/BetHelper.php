<?php

namespace App\Helper;

use App\Entity\Outcome;
use App\Entity\User;

class BetHelper
{
    /**
     * @param User $user
     * @param int $credits
     */
    public static function adjustCredits(User $user, int $credits): void
    {
        $user->setCredits($user->getCredits() + $credits);
    }

    /**
     * @param Outcome $outcome
     */
    public static function assignCredits(Outcome $outcome): void
    {
        $bets = $outcome->getBets();

        foreach ($bets as $bet) {
            $user = $bet->getUser();
            self::adjustCredits($user, self::calculateWinnings($outcome->getPayout(), $bet->getAmount()));
        }
    }

    /**
     * @param float $odds
     * @param int $amount
     * @return int
     */
    public static function calculateWinnings(float $odds, int $amount): int
    {
        return ($amount * $odds) + $amount;
    }

    /**
     * @param Outcome $outcome
     * @return array
     */
    public static function getWinners(Outcome $outcome): array
    {
        $bets = $outcome->getBets();

        $users = [];
        foreach ($bets as $bet) {
            $users[] = $bet->getUser()->getDisplayName() . ' - '. self::calculateWinnings($outcome->getPayout(), $bet->getAmount());
        }

        return $users;
    }
}
