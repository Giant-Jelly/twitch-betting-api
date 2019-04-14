<?php

namespace App\Controller;

use GuzzleHttp\Client;
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

            return new Response($request->get('hub_challenge'), 200, ['Authorization' => 'Bearer 0xbs09cssol0tpilnpkrqw2hp4h4hw']);
        }

        $data = json_decode($request->getContent(), true);

        $this->publisher->__invoke(new Update(
            'http://46.101.18.176/alerts',
            json_encode(['alert' => $data])
        ));

        return new JsonResponse($data, 200, ['Authorization' => 'Bearer 0xbs09cssol0tpilnpkrqw2hp4h4hw']);
    }

    /**
     * @Route("/subscribe", name="Subscribe", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscribeToAlerts(Request $request): Response
    {
        $client = new Client();

        $response = $client->request('POST', 'https://api.twitch.tv/helix/webhooks/hub', [
            'headers' => [
                'Authorization' => 'Bearer 0xbs09cssol0tpilnpkrqw2hp4h4hw'
            ],
            'json' => [
                'hub.callback' => 'http://46.101.18.176/alerts/twitch',
                'hub.mode' => 'subscribe',
                'hub.topic' => 'https://api.twitch.tv/helix/users/follows?first=1&to_id=26490481',
                'hub.lease_seconds' => '600'
            ]
        ]);
    }

    /**
     * @Route("/followers", name="Followers", methods={"GET"})
     *
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFollowers(): Response
    {
        $client = new Client();
        $response = $client->request('GET', 'https://api.twitch.tv/helix/users/follows?to_id=32290603', [
            'headers' => [
                'Authorization' => 'Bearer 0xbs09cssol0tpilnpkrqw2hp4h4hw'
            ]
        ]);

        return new JsonResponse($response);
    }
}
