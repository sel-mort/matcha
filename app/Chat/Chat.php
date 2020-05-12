<?php
namespace App\Chat;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        /*$cookie = $conn->httpRequest->getHeader('Cookie');
        print_r($cookie);*/

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Get the wanted receiver username
        $querystring = $from->httpRequest->getUri()->getQuery();
        parse_str($querystring, $queryarray);
        $receiver = (string) $queryarray['username'];

        // Get sender username
        $cookie = $from->httpRequest->getHeader('Cookie');
        $cookie = explode("=", $cookie[0])[1];

        // Send post request to server to get session

        $Guzzle = new Client();

        $response = $Guzzle->post(
            'http://localhost/session',
            [
                RequestOptions::JSON => [
                    'sid' => $cookie,
                ],
            ]
        );
        $content = (string) $response->getBody();
        $content = json_decode($content);
        $sender = $content->data[0];

        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s (from %s to %s)' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's', $sender, $receiver);

        foreach ($this->clients as $client) {
            // Get client username using cookie
            $cookie = $client->httpRequest->getHeader('Cookie');
            //echo "\n-----" . print_r($cookie, 1) . "-----\n";
            $cookie = explode("=", $cookie[0])[1];
            // Send post request to server to get session
            $Guzzle = new Client();
            $response = $Guzzle->post(
                'http://localhost/session',
                [
                    RequestOptions::JSON => [
                        'sid' => $cookie,
                    ],
                ]
            );
            $content = (string) $response->getBody();
            $content = json_decode($content);
            $client_username = $content->data[0];

            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                if ($client_username == $receiver) {
                    // Check if the users are are matched to each other
                    $Guzzle = new Client();
                    $response = $Guzzle->post(
                        'http://localhost/matches',
                        [
                            RequestOptions::JSON => [
                                'username0' => $sender,
                                'username1' => $receiver,
                            ],
                        ]
                    );
                    $content = (string) $response->getBody();
                    $content = json_decode($content);
                    $matches_res = $content->data[0];
                    if ((int) $matches_res == 1) {
                        //echo "--- Sender is " . $sender . " Receiver is " . $client_username . " (matching test) " . $matches_res . "---";

                        $res = array();
                        array_push($res, $msg, $sender);
                        $res = json_encode($res);
                        $client->send($res);
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
