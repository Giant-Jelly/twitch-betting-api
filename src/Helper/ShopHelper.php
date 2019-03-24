<?php

namespace App\Helper;

use App\Entity\User;

class ShopHelper
{
    /**
     * @param User $user
     * @return array
     */
    public static function getFlare(User $user): array
    {
        $flare = $user->getFlair();
        if ($flare) {
            $response = [
                'asset' => $flare->getAsset(),
                'type' => $flare->getType(),
            ];
            return $response;
        }
        return [];
    }
}
