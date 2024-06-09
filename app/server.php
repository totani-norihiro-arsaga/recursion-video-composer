<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Requests\RpcRequestHandler;
use App\Sockets\ServerSocket;

function main(): void
{
    $socket = new ServerSocket();
    try {
        $socket->listen();
        echo '接続待ちです。'.PHP_EOL;
        $clientSocket = $socket->accept();
        if ($clientSocket) {
            echo '接続されました。'.PHP_EOL;
        }
        $content = $socket->read($clientSocket);
        $rcpData = (new RpcRequestHandler($content))->handle();
        $procedure = \App\Enums\Methods::from($rcpData->getMethodNumber())->getProcedure();
        $procedure($content->getFilePath(), $content->getMediaType(), $rcpData->getArguments());
    } catch (Exception $e) {
        echo $e->getMessage().PHP_EOL;
    } finally {
        echo 'コネクションを閉じます。'.PHP_EOL;
        $socket->close();
    }
}

main();