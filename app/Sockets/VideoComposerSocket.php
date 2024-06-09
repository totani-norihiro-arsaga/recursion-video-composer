<?php

declare(strict_types=1);

namespace App\Sockets;

use Exception;
use App\Messages\Message;
use Socket;

abstract class VideoComposerSocket
{

    private const HEADER_LENGTH = 7;
    private const JSON_SIZE_LENGTH = 2;
    private const MEDIA_TYPE_SIZE_LENGTH = 1;
    private const PAYLOAD_SIZE_LENGTH = 4;
    private const MAX_RECEIVE_DATA_SIZE = 1048576;


    protected Socket $socket;
    private string $tmp_path;

    public function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->tmp_path = __DIR__.'/../../storage/';
    }

    public function read(Socket $clientSocket): Message
    {
        echo '受信します。'.PHP_EOL;
        $header = '';
        $result = socket_recv($clientSocket, $header, self::HEADER_LENGTH, MSG_WAITALL);
        if ($result === false) {
            throw new Exception(socket_strerror(socket_last_error($clientSocket)));
        }

        $jsonSize = unpack('S', substr($header, 0, self::JSON_SIZE_LENGTH))[1];
        $mediaSize = unpack('C', substr($header, self::JSON_SIZE_LENGTH, self::MEDIA_TYPE_SIZE_LENGTH))[1];
        $payloadSize = unpack('N',
            substr($header, self::JSON_SIZE_LENGTH + self::MEDIA_TYPE_SIZE_LENGTH, self::PAYLOAD_SIZE_LENGTH))[1];
        $jsonContent = socket_read($clientSocket, $jsonSize);
        $mediaType = socket_read($clientSocket, $mediaSize);

        $filePath = $this->tmp_path.date('d-m-Y-H-i-s').'.'.$mediaType;
        $fileHandler = fopen($filePath, 'a');
        $receivedPayloadSize = 0;
        while ($payloadSize > $receivedPayloadSize) {
            $data = socket_read($clientSocket, min(self::MAX_RECEIVE_DATA_SIZE, $payloadSize - $receivedPayloadSize));
            fwrite($fileHandler, $data);
            echo 'payload_size:'.$payloadSize.PHP_EOL;
            echo 'received_size:'.$receivedPayloadSize.PHP_EOL;
            $receivedPayloadSize += strlen($data);
        }

        return new Message($jsonContent, $mediaType, $filePath);
    }

    public function fileSend(Message $message): true
    {
        $fileSize = filesize($message->getFilePath());
        $jsonSize = strlen($message->getJson());
        $mediaTypeSize = strlen($message->getMediaType());
        $header = pack('S', $jsonSize).
            pack('C', $mediaTypeSize).
            pack('N', $fileSize);

        $fileHandler = fopen($message->getFilePath(), 'rb');
        $body = $message->getJson().
            $message->getMediaType().
            fread($fileHandler, self::MAX_RECEIVE_DATA_SIZE - ($jsonSize + $mediaTypeSize));
        if (!socket_write($this->socket, $header.$body, self::HEADER_LENGTH + strlen($body))) {
            throw new Exception('メッセージの送信に失敗しました。');
        }

        while (!feof($fileHandler)) {
            if (!socket_write($this->socket, fread($fileHandler, self::MAX_RECEIVE_DATA_SIZE))) {
                throw new Exception('ファイルの転送に失敗しました。');
            }
        }
        return true;
    }

    public function close()
    {
        socket_close($this->socket);
    }
}