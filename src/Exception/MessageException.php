<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class MessageException extends \Exception
{
    public function __construct(string $message)
    {
        return new Response($message);
    }
}
