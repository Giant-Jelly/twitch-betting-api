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
use App\Helper\ShopHelper;
use App\Repository\BetRepository;
use App\Repository\RoundRepository;
use App\Response\ApiResponse;
use App\Service\Alert;
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
     * @var Alert
     */
    private $alert;

    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

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
                'message' => 'You are not yet registered to bet. Run !register first'
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
            'message' => $user->getDisplayName() . ' bet ' . $bet->getAmount() . ' on ' . $outcome->getName()
        ];

        $this->alert->sendMessage($response['message']);
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
        $round = $roundRepository->getLatestOngoingRound();

        if (!$round) {
            $response = [
                'bets' => []
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }

        /** @var Bet[] $bets */
        $bets = $betRepository->findAllByRound($round);

        $response = [];
        foreach ($bets as $bet) {
            $response[] = [
                'user' => $bet->getUser()->getDisplayName(),
                'flair' => ShopHelper::getFlare($bet->getUser()),
                'badge' => ShopHelper::getBadge($bet->getUser()),
                'amount' => $bet->getAmount(),
                'outcome' => $bet->getOutcome()->getId()
            ];
        }

        $response = [
            'bets' => $response
        ];

        return ResponseHelper::getApiResponse($request, $response);
    }

    /**
     * @Route("/last-bets", name="LastBets", methods={"GET"})
     *
     * @param Request $request
     * @param RoundRepository $roundRepository
     * @param BetRepository $betRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function lastBets(Request $request, RoundRepository $roundRepository, BetRepository $betRepository): Response
    {
        $round = $roundRepository->getLatest(0);
        /** @var Bet[] $bets */
        $bets = $betRepository->findAllByRound($round);

        $winners = [];
        $losers = [];
        foreach ($bets as $bet) {
            if ($bet->getOutcome()->getWon()) {
                $winners[] = [
                    'user' => $bet->getUser()->getDisplayName(),
                    'flair' => ShopHelper::getFlare($bet->getUser()),
                    'badge' => ShopHelper::getBadge($bet->getUser()),
                    'amount' => $bet->getWinnings(),
                ];
            } else {
                $losers[] = [
                    'user' => $bet->getUser()->getDisplayName(),
                    'flair' => ShopHelper::getFlare($bet->getUser()),
                    'badge' => ShopHelper::getBadge($bet->getUser()),
                    'amount' => $bet->getAmount()
                ];
            }
        }

        $response = [
            'winners' => $winners,
            'losers' => $losers
        ];

        return ResponseHelper::getApiResponse($request, $response);
    }
}
