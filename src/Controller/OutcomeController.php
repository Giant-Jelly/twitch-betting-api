<?php

namespace App\Controller;

use App\Entity\Outcome;
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
     */
    public function newOutcome(Request $request, RoundRepository $roundRepo, OutcomeRepository $outcomeRepo): Response
    {
        $round = $roundRepo->getLatest();

        $outcome = (new Outcome())
            ->setName($request->get('name'))
            ->setPayout($request->get('payout', 0.5))
            ->setRound($round)
            ->setChoice($outcomeRepo->count(['round' => $round]) + 1)
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($outcome);
        $em->flush();

        return new Response('Outcome ' . $outcome->getName() . ' created');
    }
}
