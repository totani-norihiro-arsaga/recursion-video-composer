<?php

declare(strict_types=1);

namespace App\Sockets;

class ServerSocket extends VideoComposerSocket
{
    private const SERVER_ADDRESS = '127.0.0.1';
    private const SERVER_PORT = 9010;

    public function __construct()
    {
        parent::__construct();
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
}