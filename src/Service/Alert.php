<?php

namespace App\Service;

use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class Alert implements MessageComponentInterface
{

    protected $connections = [];

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->connections = $conn;
        $conn->send('Welcome to the sexy GiantJelly Websocket.');
        echo "New connection\n";
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        foreach ($this->connections as $key => $conn_element) {
            if ($conn === $conn_element) {
                unset($this->connections[$key]);
                break;
            }
        }
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send("Error : " . $e->getMessage());
        $conn->close();
    }

    public function onMessage(ConnectionInterface $conn, MessageInterface $msg)
    {
        $message = json_decode(trim($msg));

        print_r($msg->getPayload());
        $conn->send("For testing, im just sending back your message: " . $msg->getPayload());
    }

    public function getConnections(): array
    {
        return $this->connections;
    }

    public function sendMessage(string $message)
    {
        echo "Sending message: ".$message;
        /** @var ConnectionInterface $conn */
        foreach ($this->connections as $conn) {
            echo $message . "\n";
            $conn->send($message);
        }
    }
}
