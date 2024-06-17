<?php

declare(strict_types=1);

namespace App\Sockets;

use App\Messages\ErrorMessage;
use Exception;
use App\Messages\Message;
use Socket;

abstract class VideoComposerSocket
{

    protected const HEADER_LENGTH = 7;
    protected const JSON_SIZE_LENGTH = 2;
    protected const MEDIA_TYPE_SIZE_LENGTH = 1;
    protected const PAYLOAD_SIZE_LENGTH = 4;
    protected const MAX_RECEIVE_DATA_SIZE = 1048576;


    protected Socket $socket;
    private string $tmp_path;

    public function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->tmp_path = __DIR__.'/../../storage/tmp/';
    }

    public function read(Socket $targetSocket = null): Message
    {
        $socket = is_null($targetSocket)
            ? $this->socket
            : $targetSocket;
        echo '受信します。'.PHP_EOL;
        $header = '';
        $result = socket_recv($socket, $header, self::HEADER_LENGTH, MSG_WAITALL);
        if ($result === false) {
            throw new Exception('データの受信でエラーが発生しました。', 20);
        }
        $jsonSize = unpack('S', substr($header, 0, self::JSON_SIZE_LENGTH))[1];
        $mediaSize = unpack('C', substr($header, self::JSON_SIZE_LENGTH, self::MEDIA_TYPE_SIZE_LENGTH))[1];
        $payloadSize = unpack('N',
            substr($header, self::JSON_SIZE_LENGTH + self::MEDIA_TYPE_SIZE_LENGTH, self::PAYLOAD_SIZE_LENGTH))[1];

        if($mediaSize === 0 && $payloadSize === 0) {
            return new ErrorMessage(socket_read($socket, $jsonSize), '', '');
        }

        $jsonContent = $jsonSize
            ? socket_read($socket, $jsonSize)
            : '';
        $mediaType = $mediaSize
            ? socket_read($socket, $mediaSize)
            : '';

        $filePath = $this->tmp_path.date('d-m-Y-H-i-s-v').'.'.$mediaType;
        $fileHandler = fopen($filePath, 'a');
        if(!$fileHandler) {
            throw new Exception('何かがおかしいようです。', 21);
        }
        $receivedPayloadSize = 0;
        while ($payloadSize > $receivedPayloadSize) {
            $data = socket_read($socket, min(self::MAX_RECEIVE_DATA_SIZE, $payloadSize - $receivedPayloadSize));
            if($data === false) {
                throw new Exception('データの受信でエラーが発生しました。', 20);
            }
            fwrite($fileHandler, $data);
            echo 'payload_size:'.$payloadSize.PHP_EOL;
            echo 'received_size:'.$receivedPayloadSize.PHP_EOL;
            $receivedPayloadSize += strlen($data);
        }

        return new Message($jsonContent, $mediaType, $filePath);
    }

    public function sendFile(Message $message, Socket $targetSocket = null): true
    {
        $socket = is_null($targetSocket)
            ? $this->socket
            : $targetSocket;
        $fileSize = file_exists($message->getFilePath()) ? filesize($message->getFilePath()) : 0;
        $jsonSize = strlen($message->getJson());
        $mediaTypeSize = strlen($message->getMediaType());
        $header = pack('S', $jsonSize).
            pack('C', $mediaTypeSize).
            pack('N', $fileSize);

        $fileHandler = fopen($message->getFilePath(), 'rb');
        $body = $message->getJson().
            $message->getMediaType().
            fread($fileHandler, self::MAX_RECEIVE_DATA_SIZE - ($jsonSize + $mediaTypeSize));
        if (!socket_write($socket, $header.$body, self::HEADER_LENGTH + strlen($body))) {
            throw new Exception('メッセージの送信に失敗しました。');
        }

        while (!feof($fileHandler)) {
            if (!socket_write($socket, fread($fileHandler, self::MAX_RECEIVE_DATA_SIZE))) {
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