<?php

namespace App\Command;

use App\Service\Alert;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SocketServerCommand extends Command
{

    /**
     * @var Alert
     */
    private $alert;

    public function __construct(Alert $alert)
    {
        parent::__construct();
        $this->alert = $alert;
    }

    protected function configure()
    {
        $this->setName('twitch:alerts:server')
            ->setDescription('Start the twitch alerts web socket server')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = IoServer::factory(new HttpServer(
            new WsServer($this->alert)
        ), 8080);

        echo "Server Starting\n";
        $server->run();
    }
}
