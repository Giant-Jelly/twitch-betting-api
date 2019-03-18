<?php

namespace App\Controller;

use App\Entity\User;
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
        $user = (new User())
            ->setUsername(RequestHelper::getUsernameFromRequest($request))
            ->setDisplayName(RequestHelper::getDisplayNameFromRequest($request))
            ->setCredits(User::STARTING_CREDITS)
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new Response($user->getDisplayName() . ' has registered for betting and been awarded ' . User::STARTING_CREDITS . ' credits. Use !betting to find out how to bet!');
    }
}
