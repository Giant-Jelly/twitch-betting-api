<?php

namespace App\Controller;

use App\Entity\Bet;
use App\Entity\Outcome;
use App\Entity\Round;
use App\Entity\User;
use App\Exception\MessageException;
use App\Helper\BetHelper;
use App\Helper\RequestHelper;
use App\Response\ApiResponse;
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
     * @Route("/bet", name="Bet", methods={"GET"})
     *
     * @param Request $request
     * @return ApiResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function bet(Request $request): ApiResponse
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => RequestHelper::getUsernameFromRequest($request)]);

        if (!$user) {
            return new ApiResponse('Your user isn\'t registered to bet. Run !register first');
        }

        $round = $em->getRepository(Round::class)->getLatestOngoingRound();

        if (!$round->getOpen()) {
            return new ApiResponse('Betting is currently closed. Wait until the next round');
        }

        $outcome = $em->getRepository(Outcome::class)->findOneBy(['round' => $round, 'choice' => $request->get('outcome')]);

        if (!$outcome) {
            return new ApiResponse('That outcome doesn\'t exist');
        }

        $bet = (new Bet())
            ->setUser($user)
            ->setOutcome($outcome)
            ->setAmount($request->get('amount'));

        $em->persist($bet);
        BetHelper::adjustCredits($user, -$request->get('amount'));
        $em->flush();

        return new ApiResponse('You bet ' . $bet->getAmount() . ' on ' . $outcome->getName());
    }
}
