<?php

declare(strict_types=1);

namespace App\Requests;

use App\Messages\Message;
use App\Rpcs\RpcData;

class RpcRequestHandler
{
    public function __construct(private Message $requestMessage){
        if(!$this->requestMessage->getJson()) {
            throw new \Exception('画像処理のリクエストが存在しません。');
        }
    }

    public function handle(): RpcData
    {
        $request = json_decode($this->requestMessage->getJson());
        if(!isset($request['method']) && isset($request['arguments'])) {
            throw new \Exception('画像処理のリクエストでは無いようです');
        }
        return new RpcData($request['method'], $request['arguments']);
    }
}