<?php

declare(strict_types=1);

namespace App\Sockets;

use Exception;
use Socket;

class ClientConnectionSocket
{
    private const SERVER_ADDRESS = '127.0.0.1';
    private const SERVER_PORT = 9010;
    private const FIRST_MESSAGE_LENGTH = 32;
    private const MESSAGE_LENGTH = 1400;
    private Socket $socket;

    public function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(!socket_connect($this->socket, self::SERVER_ADDRESS, self::SERVER_PORT)) {
            echo socket_strerror(socket_last_error($this->socket));
        }
    }

    public function send(string $content): true
    {
        $contentLen = strlen($content);
        if (!socket_send($this->socket, str_pad((string)$contentLen, 32, '0', STR_PAD_LEFT), self::FIRST_MESSAGE_LENGTH, 0)) {
            throw new Exception(socket_strerror(socket_last_error($this->socket)));
        }
        echo 'ヘッダーの送信に成功しました。'.PHP_EOL;

        $offset = 0;
        while($offset < $contentLen) {
            echo '送信します。'.PHP_EOL;
            if (!$dataLen = socket_write($this->socket, substr($content, $offset), self::MESSAGE_LENGTH)) {
                echo '送信失敗です。'.PHP_EOL;
                throw new Exception(socket_strerror(socket_last_error($this->socket)));
            }
            $offset += $dataLen;
            echo $dataLen.PHP_EOL;
            echo $offset.PHP_EOL;
        }
        return true;
    }

    public function read()
    {
        $data = '';
        socket_recv($this->socket, $data, 16, MSG_WAITALL);
        echo 'データを受信しました。'.PHP_EOL;
        if (!$data) {
            throw new Exception('responseの受信に失敗しました。');
        }
        return (int)$data;
    }

    public function close()
    {
        socket_close($this->socket);
    }
}