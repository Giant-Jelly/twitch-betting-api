<?php

namespace App\Controller;

use App\Entity\Bet;
use App\Entity\Outcome;
use App\Entity\Round;
use App\Entity\User;
use App\Exception\MessageException;
use App\Helper\BetHelper;
use App\Helper\RequestHelper;
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
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function bet(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => RequestHelper::getUsernameFromRequest($request)]);

        if (!$user) {
            return new Response('Your user isn\'t registered to bet. Run !register first');
        }

        $round = $em->getRepository(Round::class)->getLatest();
        $outcome = $em->getRepository(Outcome::class)->findOneBy(['round' => $round, 'choice' => $request->get('outcome')]);

        if (!$outcome) {
            return new Response('That outcome doesn\'t exist');
        }

        $bet = (new Bet())
            ->setUser($user)
            ->setOutcome($outcome)
            ->setAmount($request->get('amount'))
        ;

        $em->persist($bet);
        BetHelper::adjustCredits($user, - $request->get('amount'));
        $em->flush();

        return new Response('You bet ' . $bet->getAmount() . ' on ' . $outcome->getName());
    }
}
