<?php

declare(strict_types=1);

namespace app\Sockets;

use app\Messages\ClientMessage;
use Exception;
use Socket;

require_once 'ClientMessage.php';

class ServerMessagingSocket
{
    private const SERVER_ADDRESS = '127.0.0.1';
    private const SERVER_PORT = 9010;
    private const MESSAGE_LENGTH = 4096;
    private Socket $socket;

    public function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_bind($this->socket, self::SERVER_ADDRESS, self::SERVER_PORT);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 60, 'usec' => 0]);
    }

    public function send(string $message, string $clientAddress, int $clientPort): true
    {
        echo $clientPort.'へメッセージ送信'.PHP_EOL;
        if (!socket_sendto($this->socket, $message, self::MESSAGE_LENGTH, MSG_EOF, $clientAddress, $clientPort)) {
            throw new Exception('メッセージの送信に失敗しました。');
        }

        return true;
    }

    public function read(): ClientMessage
    {
        $data = '';
        $clientAddress = '';
        $clientPort = 0;
        socket_recvfrom($this->socket, $data, 4096, MSG_WAITALL, $clientAddress, $clientPort);
        if (!$data) {
            throw new Exception('メッセージが届きませんでした。');
        }
        return ClientMessage::fromBinaryData($data, $clientAddress, $clientPort);
    }

    public function close()
    {
        socket_close($this->socket);
    }
}