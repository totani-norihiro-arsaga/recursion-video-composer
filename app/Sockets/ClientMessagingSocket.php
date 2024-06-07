<?php

declare(strict_types=1);

namespace app\Sockets;

use App\Messages\ClientMessage;
use Exception;
use Socket;

class ClientMessagingSocket
{
    private const CLIENT_ADDRESS = '127.0.0.1';
    private const MESSAGE_LENGTH = 4096;
    private Socket $socket;

    public function __construct(
        private int $port
    ) {
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_bind($this->socket, self::CLIENT_ADDRESS, $this->port);
    }

    public function send(string $message, string $serverAddress, int $serverPort)
    {
        if (!socket_sendto($this->socket, $message, self::MESSAGE_LENGTH, MSG_EOF, $serverAddress, $serverPort)) {
            throw new Exception('メッセージの送信に失敗しました。');
        }

        return true;
    }

    public function read()
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