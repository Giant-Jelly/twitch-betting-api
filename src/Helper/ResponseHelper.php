<?php

namespace App\Helper;

use App\Response\ApiResponse;
use App\Response\JsonApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseHelper
{
    /**
     * @param $content
     * @param Request $request
     * @return Response
     */
    public static function getApiResponse(Request $request, array $content): Response
    {
        if ($request->getMethod() == 'OPTIONS') {
            return new ApiResponse('');
        }

        if ($request->headers->get('gj-json')) {
            return new JsonApiResponse($content);
        } else {
            return new ApiResponse($content['message']);
        }
    }
}
