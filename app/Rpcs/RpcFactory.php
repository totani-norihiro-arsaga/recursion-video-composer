<?php

declare(strict_types=1);

namespace App\Rpcs;

interface RpcFactory
{
    public function define(): RpcData;
}