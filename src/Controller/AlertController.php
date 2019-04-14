<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/alerts", name="Alerts")
 *
 * Class AlertController
 * @package App\Controller
 */
class AlertController extends BaseController
{

    /**
     * @var Publisher
     */
    private $publisher;

    public function __construct(Publisher $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @Route("/twitch", name="Twitch", methods={"GET"})
     *
     * @return Response
     */
    public function handleAlert(): Response
    {
        $this->publisher->__invoke(new Update(
            'http://46.101.18.176/alerts',
            json_encode(['alert' => 'follower'])
        ));

        return new JsonResponse(['message' => 'success'], 200);
    }
}
