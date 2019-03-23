<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class RequestHelper
{
    /**
     * @param Request $request
     * @return string
     */
    public static function getUsernameFromRequest(Request $request): string
    {
        if ($request->headers->get('Nightbot-User')) {
            parse_str($request->headers->get('Nightbot-User'), $headerData);
            return $headerData['name'];
        } else {
            return $request->get('userId');
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    public static function getDisplayNameFromRequest(Request $request): string
    {
        if ($request->headers->get('Nightbot-User')) {
            parse_str($request->headers->get('Nightbot-User'), $headerData);
            return $headerData['displayName'];
        } else {
            return $request->get('username');
        }
    }
}
