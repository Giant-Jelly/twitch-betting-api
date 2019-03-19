<?php

namespace App\Controller;

use App\Entity\Outcome;
use App\Exception\MessageException;
use App\Repository\OutcomeRepository;
use App\Repository\RoundRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/outcome", name="ApiOutcome")
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

        return new Response('Outcome ' . $outcome->getName() . ' created');
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
            return new Response('No current ongoing round');
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

        return new Response('Outcomes have been repeated from ' . $round->getName());
    }
}
