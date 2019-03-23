<?php

namespace App\Controller;

use App\Helper\RequestHelper;
use App\Response\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="ApiTest")
 *
 * Class BaseController
 * @package App\Controller
 */
class BaseController extends AbstractController
{
    /**
     * @Route("/test", name="Test", methods={"GET"})
     *
     * @param Request $request
     * @return ApiResponse
     */
    public function test(Request $request): ApiResponse
    {
        return new ApiResponse(print_r($request->headers));
    }
}
