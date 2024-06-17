<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Enums\Methods;
use App\Messages\ErrorMessage;
use App\Messages\MessageGenerator;
use App\Sockets\ClientSocket;

function main(): void
{
    $outputPath = __DIR__.'/../storage/outputs/';
    $inputPath = __DIR__.'/../storage/inputs/';
    try {
        $connectedSocket = new ClientSocket();
    } catch (Exception $e) {
        echo 'サーバーとの接続でエラーが発生しました。サービスが停止している可能性があります。';
        exit();
    }

    echo 'ファイル名(拡張子まで)を入力してください。'.PHP_EOL;
    $fileName = trim(fgets(STDIN));
    $filePath = $inputPath.$fileName;
    if(!file_exists($filePath)) {
        echo 'storage/inputs/に指定のファイルが存在しません。改めてご確認ください。';
        $connectedSocket->close();
        exit();
    }
    echo 'ファイルへ実施する操作を選択してください。'.PHP_EOL;
    echo Methods::getChoices();
    $methodNumber = (int)trim(fgets(STDIN));
    echo $methodNumber.'が選択されました。';
    try {
        $requestMessage = MessageGenerator::generateRequestMessage($filePath, $methodNumber);
        $connectedSocket->sendFile($requestMessage);
        $response = $connectedSocket->read();
        if ($response instanceof ErrorMessage) {
            throw new Exception($response->getSuggestion(), $response->getCode());
        }
        echo $response->getFilePath().PHP_EOL;
        copy($response->getFilePath(),$outputPath.pathinfo($response->getFilePath(), PATHINFO_BASENAME));
        unlink($response->getFilePath());
        echo '処理が完了しました。成果物：'.$outputPath.'をご確認ください。';
    } catch (Exception $e) {
        echo $e->getMessage();
    } finally {
        $connectedSocket->close();
    }
}

main();