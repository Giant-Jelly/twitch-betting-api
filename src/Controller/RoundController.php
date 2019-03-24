<?php

namespace App\Controller;

use App\Entity\Outcome;
use App\Entity\Round;
use App\Helper\BetHelper;
use App\Helper\ResponseHelper;
use App\Helper\RoundHelper;
use App\Repository\OutcomeRepository;
use App\Repository\RoundRepository;
use App\Response\ApiResponse;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/round", name="ApiRound", methods={"GET", "OPTIONS"})
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

        $response = [
            'message' => 'New betting "' . $round->getName() . '" round created'
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }

    /**
     * @Route("/end", name="End", methods={"GET"})
     *
     * @param Request $request
     * @param RoundRepository $repo
     * @param OutcomeRepository $outcomeRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function endRound(Request $request, RoundRepository $repo, OutcomeRepository $outcomeRepository): Response
    {
        /** @var Round $round */
        $round = $repo->getLatestOngoingRound();

        if (!$round) {
            $response = [
                'message' => 'There are no currently ongoing rounds'
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }

        $round->setFinished(true);

        $outcome = $outcomeRepository->findOneBy(['round' => $round, 'choice' => $request->get('outcome')]);
        $outcome->setWon(true);

        BetHelper::assignCredits($outcome);

        $this->getDoctrine()->getManager()->flush();

        $winners = BetHelper::getWinners($outcome);

        $response = [
            'message' => 'Round "' . $round->getName() . '" ended. ' . $outcome->getName() . ' won! Winners: ' . implode(' | ', $winners)
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }

    /**
     * @Route("/open", name="Open", methods={"GET"})
     *
     * @param Request $request
     * @param RoundRepository $roundRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function openBetting(Request $request, RoundRepository $roundRepository): Response
    {
        $round = $roundRepository->getLatestOngoingRound();
        $round->setOpen(true);
        $this->getDoctrine()->getManager()->flush();

        $response = [
            'message' => 'Betting round OPEN! Start betting with with !bet'
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }

    /**
     * @Route("/close", name="Close", methods={"GET"})
     *
     * @param Request $request
     * @param RoundRepository $roundRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function closeBetting(Request $request, RoundRepository $roundRepository): Response
    {
        $round = $roundRepository->getLatestOngoingRound();
        $round->setOpen(false);
        $this->getDoctrine()->getManager()->flush();

        $response = [
            'message' => 'Betting round CLOSED!'
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }

    /**
     * @Route("/status", name="Status", methods={"GET"})
     *
     * @param Request $request
     * @param RoundRepository $roundRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function roundStatus(Request $request, RoundRepository $roundRepository): Response
    {
        $status = RoundHelper::getRoundStatus($roundRepository->getLatest());

        $response = [
            'status' => $status,
            'message' => $status
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }

    /**
     * @Route("/repeat", name="Repeat", methods={"GET"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function repeatRound(Request $request, EntityManagerInterface $em): Response
    {
        $currentRound = $em->getRepository(Round::class)->getLatestOngoingRound();

        if ($currentRound) {
            $response = [
                'message' => 'Cannot repeat round while there is an active round'
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }
        $round = $em->getRepository(Round::class)->getLatest();

        $newRound = (new Round())
            ->setName($round->getName())
            ->setOpen(true)
            ->setFinished(false);

        foreach ($round->getOutcomes() as $outcome) {
            $o = (new Outcome())
                ->setChoice($outcome->getChoice())
                ->setName($outcome->getName())
                ->setPayout($outcome->getPayout())
                ->setRound($newRound)
                ->setColour($outcome->getColour());

            $em->persist($o);
        }

        $em->flush();

        $response = [
            'message' => 'Round ' . $round->getName() . ' has been repeated'
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }
}
