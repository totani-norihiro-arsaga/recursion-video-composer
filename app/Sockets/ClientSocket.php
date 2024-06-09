<?php

declare(strict_types=1);

namespace App\Sockets;

use Exception;

class ClientSocket extends VideoComposerSocket
{
    private const SERVER_ADDRESS = '127.0.0.1';
    private const SERVER_PORT = 9010;
    public function __construct()
    {
        parent::__construct();
        if(!socket_connect($this->socket, self::SERVER_ADDRESS, self::SERVER_PORT)) {
            echo socket_strerror(socket_last_error($this->socket));
            throw new Exception('ソケットの接続でエラーが発生しました。');
        }
    }
}