<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Sockets\ServerConnectionSocket;

function main(): void
{
    $socket = new ServerConnectionSocket();
    try {
        $socket->listen();
        echo '接続待ちです。'.PHP_EOL;
        $clientSocket = $socket->accept();
        if ($clientSocket) {
            echo '接続されました。'.PHP_EOL;
        }

        $content = $socket->read($clientSocket);
        $file_handler = fopen(__DIR__.'/../result.mp4', 'wb');
        fwrite($file_handler, $content);

        $socket->send('200', $clientSocket);
        echo 'コンテンツを受信しました。'.PHP_EOL;
    } catch (Exception $e) {
        echo $e->getMessage().PHP_EOL;
        $socket->send('500', $clientSocket);
    } finally {
        echo 'コネクションを閉じます。'.PHP_EOL;
        $socket->close();
    }
}

main();