<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

class ApiResponse extends Response
{
    /**
     * ApiResponse constructor.
     * @param string $content
     * @param int $code
     */
    public function __construct(string $content, int $code = 200)
    {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET,POST,PUT,DELETE',
            'Access-Control-Allow-Headers' => 'authorization, content-type'
        ];
        parent::__construct($content, $code, $headers);
    }
}
