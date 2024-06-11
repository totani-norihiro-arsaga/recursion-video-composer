<?php

declare(strict_types=1);

namespace App\Rpcs;

use App\Enums\Methods;

class ChangeRateFactory implements RpcFactory
{
    public function define(): RpcData
    {
        $methodNumber = Methods::ChangeResolution;

        echo 'width(px)を指定してください。'.PHP_EOL;
        $width = (int)trim(fgets(STDIN));
        $arguments['width'] = $width;

        echo 'height(px)を指定してください。'.PHP_EOL;
        $height = (int)trim(fgets(STDIN));
        $arguments['height'] = $height;

        return new RpcData($methodNumber->value, $arguments);
    }
}