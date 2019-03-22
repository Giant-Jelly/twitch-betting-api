<?php

namespace App\Controller;

use App\Entity\Round;
use App\Helper\BetHelper;
use App\Repository\OutcomeRepository;
use App\Repository\RoundRepository;
use App\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/round", name="ApiRound")
 *
 * Class RoundController
 * @package App\Controller
 */
class RoundController extends BaseController
{
    /**
     * @Route("/new", name="New", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function newRound(Request $request): Response
    {
        $round = (new Round)
            ->setName($request->get('name'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($round);
        $em->flush();

        return new ApiResponse('New betting "' . $round->getName() . '" round created');
    }

    /**
     * @Route("/end", name="End", methods={"GET"})
     *
     * @param Request $request
     * @param RoundRepository $repo
     * @param OutcomeRepository $outcomeRepository
     * @return ApiResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function endRound(Request $request, RoundRepository $repo, OutcomeRepository $outcomeRepository): ApiResponse
    {
        /** @var Round $round */
        $round = $repo->getLatestOngoingRound();

        if (!$round) {
            return new ApiResponse('There are no currently ongoing rounds');
        }

        $round->setFinished(true);

        $outcome = $outcomeRepository->findOneBy(['round' => $round, 'choice' => $request->get('outcome')]);
        $outcome->setWon(true);

        BetHelper::assignCredits($outcome);

        $this->getDoctrine()->getManager()->flush();

        $winners = BetHelper::getWinners($outcome);

        return new ApiResponse('Round "' . $round->getName() . '" ended. ' . $outcome->getName() . ' won! Winners: ' . implode(' | ', $winners));
    }

    /**
     * @Route("/open", name="Open", methods={"GET"})
     *
     * @param RoundRepository $roundRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function openBetting(RoundRepository $roundRepository): Response
    {
        $round = $roundRepository->getLatestOngoingRound();
        $round->setOpen(true);
        $this->getDoctrine()->getManager()->flush();

        return new ApiResponse('Betting round OPEN! Start betting with with !bet');
    }

    /**
     * @Route("/close", name="Close", methods={"GET"})
     *
     * @param RoundRepository $roundRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function closeBetting(RoundRepository $roundRepository): Response
    {
        $round = $roundRepository->getLatestOngoingRound();
        $round->setOpen(false);
        $this->getDoctrine()->getManager()->flush();

        return new ApiResponse('Betting round CLOSED!');
    }
}
