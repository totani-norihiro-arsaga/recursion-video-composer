<?php

declare(strict_types=1);

namespace App\Rpcs;

use App\Enums\Methods;

class ConvertToAudioFactory implements RpcFactory
{
    public function define(): RpcData
    {
        $methodNumber = Methods::ChangeResolution;
        $arguments = [];

        return new RpcData($methodNumber->value, $arguments);
    }
}