<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Enums\Methods;
use App\Requests\RequestMessageGenerator;
use App\Sockets\ClientSocket;

function main(): void
{
    $connectedSocket = new ClientSocket();

    echo 'ファイルのパス(拡張子まで)を入力してください。'.PHP_EOL;
    $filePath = trim(fgets(STDIN));
    echo 'ファイルへ実施する操作を選択してください。'.PHP_EOL;
    echo Methods::getChoices();
    $methodNumber = (int)trim(fgets(STDIN));
    try {
        $requestMessage = RequestMessageGenerator::generate($filePath, $methodNumber);
        var_dump($requestMessage);
        $connectedSocket->fileSend($requestMessage);
    }catch(Exception $e){
        echo $e->getMessage();
    }
}

main();