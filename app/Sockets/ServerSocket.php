<?php

declare(strict_types=1);

namespace App\Sockets;

use App\Messages\Message;
use Exception;
use Socket;

class ServerSocket extends VideoComposerSocket
{
    private const SERVER_ADDRESS = '127.0.0.1';
    private const SERVER_PORT = 9010;

    public function __construct()
    {
        parent::__construct();
        socket_bind($this->socket, self::SERVER_ADDRESS, self::SERVER_PORT);
    }

    public function listen(): bool
    {
        return socket_listen($this->socket);
    }

    public function accept()
    {
        return socket_accept($this->socket);
    }

    public function sendError(Socket $socket, Message $message) {
        $jsonSize = strlen($message->getJson());
        $header = pack('S', $jsonSize).
            pack('C', 0).
            pack('N', 0);

        $body = $message->getJson();
        if (!socket_write($socket, $header.$body, self::HEADER_LENGTH + strlen($body))) {
            echo 'エラーメッセージの送信に失敗しました。接続が切れている可能性があります。';
        }
        return true;
    }
}