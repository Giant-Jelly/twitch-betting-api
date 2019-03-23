<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonApiResponse extends JsonResponse
{
    /**
     * ApiResponse constructor.
     * @param array $content
     * @param int $code
     */
    public function __construct(array $content, int $code = 200)
    {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET,POST,PUT,DELETE',
            'Access-Control-Allow-Headers' => 'authorization, content-type, x-json'
        ];
        parent::__construct($content, $code, $headers);
    }
}
