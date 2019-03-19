<?php

namespace App\Controller;

use App\Entity\User;
use App\Helper\BetHelper;
use App\Helper\RequestHelper;
use App\Repository\UserRepository;
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
            ->setCredits(User::STARTING_CREDITS);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new Response($user->getDisplayName() . ' has registered for betting and been awarded ' . User::STARTING_CREDITS . ' credits. Use !commands to find out how to bet!');
    }

    /**
     * @Route("/free-credits", name="FreeCredits", methods={"GET"})
     *
     * @param Request $request
     * @param UserRepository $repo
     * @return Response
     */
    public function freeCredits(Request $request, UserRepository $repo): Response
    {
        $user = $repo->findOneBy(['username' => RequestHelper::getUsernameFromRequest($request)]);

        if (!$user) {
            return new Response('Your user isn\'t registered to bet. Run !register first');
        }

        if ($user->getCreditRedemptionDate()->format('Y-m-d') >= (new \DateTime())->format('Y-m-d')) {
            return new Response('You have already redeemed your free credits today. Come back tomorrow');
        }

        BetHelper::adjustCredits($user, User::REDEEMABLE_CREDIT_AMOUNT);
        $user->setCreditRedemptionDate((new \DateTime()));
        $this->getDoctrine()->getManager()->flush();

        return new Response('You have redeemed your daily credits. ' . User::REDEEMABLE_CREDIT_AMOUNT . ' credits have been added to your account');
    }

    /**
     * @Route("/leaderboard", name="Leaderboard", methods={"GET"})
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function leaderboard(Request $request, UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['credits' => 'DESC'], 5);

        $response = '';
        foreach ($users as $key => $user) {
            $response .= $key + 1 . '. ' . $user->getDisplayName() . ' - ' . $user->getCredits() . ' | ';
        };

        return new Response($response);
    }

    /**
     * @Route("/credits", name="credits", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function credits(Request $request): Response
    {
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy([
            'username' => RequestHelper::getUsernameFromRequest($request)
        ]);

        return new Response('You have ' . $user->getCredits() . ' credits');
    }

    /**
     * @Route("/request", name="Request", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function request(Request $request): Response
    {
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy([
            'username' => RequestHelper::getUsernameFromRequest($request)
        ]);

        return new Response($user->getDisplayName() . ' has spent their credits on a request @GiantJelly');
    }
}
