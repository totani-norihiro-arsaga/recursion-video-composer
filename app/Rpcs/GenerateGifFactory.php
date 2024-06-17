<?php

declare(strict_types=1);

namespace App\Rpcs;

use App\Enums\Methods;

class GenerateGifFactory implements RpcFactory
{
    public function define(): RpcData
    {
        $methodNumber = Methods::GenerateGif;

        echo '切り取りの始点指定してください(HH:MM:SS)'.PHP_EOL;
        $start = trim(fgets(STDIN));
        $arguments['start'] = $start;

        echo '切り取りの終点指定してください(HH:MM:SS)'.PHP_EOL;
        $end = trim(fgets(STDIN));
        $arguments['duration'] = $end;

        return new RpcData($methodNumber->value, $arguments);
    }
}