<?php

declare(strict_types=1);

namespace App\Rpcs;

use App\Enums\Methods;

class CompressProcessFactory implements RpcFactory
{
    public function define(): RpcData
    {
        $methodNumber = Methods::Compress;
        $arguments = [1,2];

        return new RpcData($methodNumber->value, $arguments);
    }
}