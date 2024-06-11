<?php

declare(strict_types=1);

namespace App\Rpcs;

use App\Enums\Methods;

class GenerateGifFactory implements RpcFactory
{
    public function define(): RpcData
    {
        $methodNumber = Methods::ChangeResolution;

        echo '切り取りの始点指定してください(HH:MM:SS)'.PHP_EOL;
        $start = (int)trim(fgets(STDIN));
        $arguments['width'] = $start;

        echo '切り取りの終点指定してください(HH:MM:SS)'.PHP_EOL;
        $end = (int)trim(fgets(STDIN));
        $arguments['height'] = $end;

        return new RpcData($methodNumber->value, $arguments);
    }
}