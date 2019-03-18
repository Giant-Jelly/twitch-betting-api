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
        parse_str($request->headers->get('Nightbot-User'), $headerData);
        return $headerData['name'];
    }
}
