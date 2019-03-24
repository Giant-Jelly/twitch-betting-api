<?php

namespace App\Controller;

use App\Entity\User;
use App\Helper\BetHelper;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Repository\UserRepository;
use App\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/user", name="ApiUser", methods={"GET", "OPTIONS"})
 *
 * Class UserController
 * @package App\Controller
 */
class UserController extends BaseController
{
    const REQUEST_PRICE = 10000;

    /**
     * @Route("/register", name="Register", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy([
            'username' => RequestHelper::getUsernameFromRequest($request)
        ]);

        if ($user) {
            $response = [
                'message' => $user->getDisplayName() . ' has already registered for betting. Use !betting for more instructions'
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }

        $user = (new User())
            ->setUsername(RequestHelper::getUsernameFromRequest($request))
            ->setDisplayName(RequestHelper::getDisplayNameFromRequest($request))
            ->setCredits(User::STARTING_CREDITS);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $response = [
            'message' => $user->getDisplayName() . ' has registered for betting and been awarded ' . User::STARTING_CREDITS . ' credits. Use !betting to find out how to bet.'
        ];
        return ResponseHelper::getApiResponse($request, $response);
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
            $response = [
                'message' => 'Your user isn\'t registered to bet. Run !register first'
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }

        if ($user->getCreditRedemptionDate()->format('Y-m-d') >= (new \DateTime())->format('Y-m-d')) {
            $response = [
                'message' => 'You have already redeemed your free credits today. Come back tomorrow'
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }

        BetHelper::adjustCredits($user, User::REDEEMABLE_CREDIT_AMOUNT);
        $user->setCreditRedemptionDate((new \DateTime()));
        $this->getDoctrine()->getManager()->flush();

        $response = [
            'message' => 'You have redeemed your daily credits. ' . User::REDEEMABLE_CREDIT_AMOUNT . ' credits have been added to your account'
        ];
        return ResponseHelper::getApiResponse($request, $response);
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

        $entries = '';
        foreach ($users as $key => $user) {
            $entries .= $key + 1 . '. ' . $user->getDisplayName() . ' - ' . $user->getCredits() . ' | ';
        };

        $response = [
            'message' => $entries
        ];
        return ResponseHelper::getApiResponse($request, $response);
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

        $response = [
            'message' => 'You have ' . $user->getCredits() . ' credits'
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }

//    /**
//     * @Route("/request", name="Request", methods={"GET"})
//     *
//     * @param Request $request
//     * @return Response
//     */
//    public function request(Request $request): Response
//    {
//        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy([
//            'username' => RequestHelper::getUsernameFromRequest($request)
//        ]);
//
//        if ($user->getCredits() < self::REQUEST_PRICE) {
//            $response = [
//                'message' => 'You do not have enough credits to make a request. You have ' . $user->getCredits() . '. You need ' . self::REQUEST_PRICE
//            ];
//            return ResponseHelper::getApiResponse($request, $response);
//        }
//
//        BetHelper::adjustCredits($user, -self::REQUEST_PRICE);
//        $this->getDoctrine()->getManager()->flush();
//
//        $response = [
//            'message' => $user->getDisplayName() . ' has spent their credits on a request @GiantJelly. (Matt and Nathan will fulfill your request in a moment.)'
//        ];
//        return ResponseHelper::getApiResponse($request, $response);
//    }
}
