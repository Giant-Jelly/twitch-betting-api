<?php

namespace App\Controller;

use App\Entity\Bet;
use App\Entity\Outcome;
use App\Entity\Round;
use App\Entity\User;
use App\Exception\MessageException;
use App\Helper\BetHelper;
use App\Helper\RequestHelper;
use App\Helper\ResponseHelper;
use App\Repository\BetRepository;
use App\Repository\RoundRepository;
use App\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/betting", name="ApiBetting", methods={"GET", "OPTIONS"})
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
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function bet(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => RequestHelper::getUsernameFromRequest($request)]);

        if (!$user) {
            $response = [
                'message' => 'Your user isn\'t registered to bet. Run !register first'
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }

        $round = $em->getRepository(Round::class)->getLatestOngoingRound();

        if (!$round->getOpen()) {
            $response = [
                'message' => 'Betting is currently closed. Wait until the next round'
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }

        $outcome = $em->getRepository(Outcome::class)->findOneBy(['round' => $round, 'choice' => $request->get('outcome')]);

        if (!$outcome) {
            $response = [
                'message' => 'That outcome doesn\'t exist'
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }

        $bet = (new Bet())
            ->setUser($user)
            ->setOutcome($outcome)
            ->setAmount($request->get('amount'));

        $em->persist($bet);
        BetHelper::adjustCredits($user, -$request->get('amount'));
        $em->flush();


        $response = [
            'message' => 'You bet ' . $bet->getAmount() . ' on ' . $outcome->getName()
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }

    /**
     * @Route("/list", name="List", methods={"GET"})
     *
     * @param Request $request
     * @param BetRepository $betRepository
     * @param RoundRepository $roundRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function list(Request $request, BetRepository $betRepository, RoundRepository $roundRepository): Response
    {
        /** @var Bet[] $bets */
        $bets = $betRepository->findAllByRound($roundRepository->getLatestOngoingRound());

        $response = [];
        foreach ($bets as $bet) {
            $response['bets'][] = [
                'user' => $bet->getUser()->getDisplayName(),
                'amount' => $bet->getAmount(),
                'outcome' => $bet->getOutcome()->getId()
            ];
        }

        return ResponseHelper::getApiResponse($request, $response);
    }

    /**
     * @Route("/last-bets", name="LastBets", methods={"GET"})
     *
     * @param Request $request
     * @param RoundRepository $roundRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function lastBets(Request $request, RoundRepository $roundRepository, BetRepository $betRepository): Response
    {
        $round = $roundRepository->getLatest(1);
        /** @var Bet[] $bets */
        $bets = $betRepository->findAllByRound($round);

        $entries = [];
        foreach ($bets as $bet) {
            if ($bet->getOutcome()->getWon()) {
                $entries['winners'] = [
                    'user' => $bet->getUser()->getDisplayName(),
                    'amount' => $bet->getWinnings(),
                ];
            } else {
                $entries['losers'] = [
                    'user' => $bet->getUser()->getDisplayName(),
                    'amount' => $bet->getAmount()
                ];
            }
        }

        $response = [
            $entries
        ];

        return ResponseHelper::getApiResponse($request, $response);
    }
}
