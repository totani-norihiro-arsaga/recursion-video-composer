<?php

declare(strict_types=1);

namespace App\Rpcs;

use App\Enums\Methods;

class ChangeResolutionFactory implements RpcFactory
{
    public function define(): RpcData
    {
        $methodNumber = Methods::ChangeResolution;

        echo '変更後の解像度を指定してください。'.PHP_EOL;
        $dpi = (int)trim(fgets(STDIN));
        $arguments = ['dpi' => $dpi];
        
        return new RpcData($methodNumber->value, $arguments);
    }
}