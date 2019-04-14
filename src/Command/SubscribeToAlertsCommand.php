<?php

namespace App\Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SubscribeToAlertsCommand extends Command
{
    protected function configure()
    {
        $this->setName('twitch:alerts:subscribe')
            ->setDescription('subscribe to twitch events. This needs to be run before every stream and lasts 1 day');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();

        $response = $client->request('POST', 'https://api.twitch.tv/helix/webhooks/hub', [
            'headers' => [
                'Authorization' => 'Bearer 0xbs09cssol0tpilnpkrqw2hp4h4hw'
            ],
            'json' => [
                'hub.callback' => 'http://46.101.18.176/alerts/twitch',
                'hub.mode' => 'subscribe',
                'hub.topic' => 'https://api.twitch.tv/helix/users/follows?first=1&to_id=1337',
                'hub.lease_seconds' => '600'
            ]
        ]);

        $output->writeln($response->getBody() .' - '. $response->getStatusCode());
    }
}
