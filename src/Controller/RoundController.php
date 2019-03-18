<?php

namespace App\Controller;

use App\Entity\Round;
use App\Repository\OutcomeRepository;
use App\Repository\RoundRepository;
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

        return new Response('New betting "'. $round->getName() .'" round created');
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
        $round = $repo->getLatest();
        $round->setFinished(true);

        $outcome = $outcomeRepository->findOneBy(['round' => $round, 'choice' => $request->get('outcome')]);
        $outcome->setWon(true);
        $this->getDoctrine()->getManager()->flush();

        return new Response('Round "' . $round->getName() . '" ended. ' . $outcome->getName() . ' won!');
    }
}
