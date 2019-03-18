<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/betting", name="ApiBetting")
 *
 * Class BetController
 * @package App\Controller
 */
class BetController extends BaseController
{
    /**
     * @Route("/bet", name="Bet", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function bet(Request $request): Response
    {


        return new Response('You bet');
    }
}
