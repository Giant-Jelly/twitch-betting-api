<?php

namespace App\Controller;

use App\Entity\Outcome;
use App\Exception\MessageException;
use App\Helper\BetHelper;
use App\Helper\ResponseHelper;
use App\Repository\OutcomeRepository;
use App\Repository\RoundRepository;
use App\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/outcome", name="ApiOutcome", methods={"GET", "OPTIONS"})
 *
 * Class OutcomeController
 * @package App\Controller
 */
class OutcomeController extends BaseController
{
    /**
     * @Route("/new", name="New", methods={"GET"})
     *
     * @param Request $request
     * @param RoundRepository $roundRepo
     * @param OutcomeRepository $outcomeRepo
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function newOutcome(Request $request, RoundRepository $roundRepo, OutcomeRepository $outcomeRepo): Response
    {
        $round = $roundRepo->getLatest();

        $outcome = (new Outcome())
            ->setName($request->get('name'))
            ->setPayout($request->get('payout', 0.5))
            ->setRound($round)
            ->setChoice($outcomeRepo->count(['round' => $round]) + 1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($outcome);
        $em->flush();

        $response = [
            'message' => 'Outcome ' . $outcome->getName() . ' created'
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }

    /**
     * @Route("/repeat", name="Repeat", methods={"GET"})
     *
     * @param Request $request
     * @param RoundRepository $roundRepo
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function repeatOutcomes(Request $request, RoundRepository $roundRepo): Response
    {
        //Get second latest round
        $round = $roundRepo->getLatest(1);
        $latestRound = $roundRepo->getLatestOngoingRound();

        if (!$latestRound) {
            $response = [
                'message' => 'No current ongoing round'
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }

        $em = $this->getDoctrine()->getManager();

        foreach ($round->getOutcomes() as $outcome) {
            $o = (new Outcome())
                ->setChoice($outcome->getChoice())
                ->setName($outcome->getName())
                ->setPayout($outcome->getPayout())
                ->setRound($latestRound);

            $em->persist($o);
        }

        $em->flush();

        $response = [
            'message' => 'Outcomes have been repeated from ' . $round->getName()
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }

    /**
     * @Route("/list", name="List", methods={"GET"})
     *
     * @param Request $request
     * @param OutcomeRepository $outcomeRepository
     * @param RoundRepository $roundRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function listOutcomes(Request $request, OutcomeRepository $outcomeRepository, RoundRepository $roundRepository): Response
    {
        $round = $roundRepository->getLatestOngoingRound();
        /** @var Outcome[] $outcomes */
        $outcomes = $outcomeRepository->findBy(['round' => $round], ['choice' => 'ASC']);

        if (count($outcomes) < 1) {
            $response = [
                'message' => 'There are no outcomes currently'
            ];
            return ResponseHelper::getApiResponse($request, $response);
        }

        $message = '';
        $entries = [];

        foreach ($outcomes as $outcome) {
            $message .= '| '.$outcome->getChoice() . '. ' . $outcome->getName() .' |';
            $entries[] = [
                'id' => $outcome->getId(),
                'name' => $outcome->getName(),
                'payout' => $outcome->getPayout(),
                'totalBets' => BetHelper::getTotalBetsAmount($outcome)
            ];
        }

        $response = [
            'outcomes' => $entries,
            'message' => $message
        ];
        return ResponseHelper::getApiResponse($request, $response);
    }
}
