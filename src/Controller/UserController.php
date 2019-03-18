<?php

namespace App\Controller;

use App\Helper\RequestHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/user", name="ApiUser")
 *
 * Class UserController
 * @package App\Controller
 */
class UserController extends BaseController
{
    /**
     * @Route("/register", name="Register", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        $username = RequestHelper::getUsernameFromRequest($request);

        return new Response();
    }
}
