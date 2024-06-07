<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Sockets\ClientConnectionSocket;

function main(): void
{
    $connectedSocket = new ClientConnectionSocket();

    echo 'ファイル名を入力してください。'.PHP_EOL;
    $fileName = trim(fgets(STDIN));
    $fileHandler = fopen($fileName.".mp4", 'rb');
    $content = '';
    while (!feof($fileHandler)) {
        $content .= fread($fileHandler, 4096);
    }
    fclose($fileHandler);
    try {
        $connectedSocket->send($content);
        $response = $connectedSocket->read();
        $response === 200
            ? print '送信に成功しました。'
            : print '送信に失敗しました。';
    } catch (Exception $e) {
        echo $e->getMessage();
    } finally {
        $connectedSocket->close();
    }
}

main();