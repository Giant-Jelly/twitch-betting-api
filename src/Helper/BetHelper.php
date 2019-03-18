<?php

namespace App\Helper;

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
}
