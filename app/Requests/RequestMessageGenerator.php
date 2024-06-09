<?php

declare(strict_types=1);

namespace App\Requests;

use App\Enums\Methods;
use App\Messages\Message;

class RequestMessageGenerator
{
    static function generate($filePath, $methodNumber)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $json = Methods::from($methodNumber)->getRcpData()->define()->getJson();
        var_dump($extension, $json);
        return new Message($json, $extension, $filePath);
    }
}