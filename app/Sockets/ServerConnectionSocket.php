<?php

declare(strict_types=1);

namespace App\Sockets;

use Exception;
use Socket;

class ServerConnectionSocket
{
    private const SERVER_ADDRESS = '127.0.0.1';
    private const MESSAGE_LENGTH = 1400;
    private const SERVER_PORT = 9010;
    private Socket $socket;

    public function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_bind($this->socket, self::SERVER_ADDRESS, self::SERVER_PORT);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 60, 'usec' => 0]);
    }

    public function listen(): bool
    {
        return socket_listen($this->socket);
    }

    public function accept()
    {
        return socket_accept($this->socket);
    }

    public function send(string $message, Socket $socket): true
    {

        if (!socket_send($socket, str_pad($message, 16, '0', STR_PAD_LEFT), 16, MSG_EOF)) {
            throw new Exception('メッセージの送信に失敗しました。');
        }

        return true;
    }

    public function read(Socket $clientSocket)
    {
        echo '受信します。'.PHP_EOL;
        $contentLen = '';
        $result = socket_recv($clientSocket, $contentLen, 32, MSG_WAITALL);
        if ($result === false) {
            throw new Exception(socket_strerror(socket_last_error($clientSocket)));
        }
        $contentLen = (int)$contentLen;
        echo 'コンテンツの大きさ：'.$contentLen.PHP_EOL;

        $data = '';
        $receivedDataLen = 0;
        while ($contentLen > $receivedDataLen) {
            $result = socket_read($clientSocket, min(self::MESSAGE_LENGTH, $contentLen - $receivedDataLen));
            if ($result === false) {
                throw new Exception('メッセージの受信に失敗しました。');
            }
            $data .= $result;
            $receivedDataLen += strlen($result);
            echo $contentLen.PHP_EOL;
            echo strlen($result).PHP_EOL;
            echo $receivedDataLen.PHP_EOL;
        }
        echo strlen($data).'バイト受信しました。';

        return $data;
    }

    public function close()
    {
        socket_close($this->socket);
    }
}