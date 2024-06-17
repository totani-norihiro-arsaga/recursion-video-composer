<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Enums\Methods;
use App\Messages\Message;
use App\Messages\MessageGenerator;
use App\Requests\RpcRequestHandler;
use App\Sockets\ServerSocket;

function main(): void
{
    $socket = new ServerSocket();
    $socket->listen();
    echo '接続待ちです。'.PHP_EOL;
    $clientSocket = $socket->accept();
    if (!$clientSocket) {
        echo 'ソケットの接続に失敗しました。';
        $socket->close();
        exit();
    }
    try {
        $content = $socket->read($clientSocket);
        $rpcData = (new RpcRequestHandler($content))->handle();
        $procedure = Methods::from($rpcData->getMethodNumber())->getProcedure();
        $outputPath = $procedure($content->getFilePath(), $rpcData->getArguments());
        unlink($content->getFilePath());
        $message = new Message('', pathinfo($outputPath, PATHINFO_EXTENSION), $outputPath);
        $socket->sendFile($message, $clientSocket);
        unlink($outputPath);
    } catch (Exception $e) {
        echo $e->getMessage().PHP_EOL;
        $socket->sendError($clientSocket, MessageGenerator::generateErrorMessage($e));
    } finally {
        echo 'コネクションを閉じます。'.PHP_EOL;
        $socket->close();
    }
}

main();