<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/twitch", name="Twitch", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function handleAlert(Request $request): Response
    {
        if ($request->get('hub_challenge')) {
            $this->publisher->__invoke(new Update(
                'http://46.101.18.176/alerts',
                json_encode(['challenge' => $request->query->all()])
            ));

            return new JsonResponse($request->get('hub_challenge'), 200, ['Authorization' => 'Bearer 0xbs09cssol0tpilnpkrqw2hp4h4hw']);
        }

        $this->publisher->__invoke(new Update(
            'http://46.101.18.176/alerts',
            json_encode(['alert' => $request->request->get('data')])
        ));

        return new JsonResponse($request->request->get('data'), 200, ['Authorization' => 'Bearer 0xbs09cssol0tpilnpkrqw2hp4h4hw']);
    }
}
