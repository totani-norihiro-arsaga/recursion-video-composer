<?php

declare(strict_types=1);

namespace App\Messages;

use App\Enums\Methods;
use Exception;

class MessageGenerator
{
    static function generateRequestMessage($filePath, $methodNumber): Message
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $json = Methods::from($methodNumber)->getRcpData()->define()->getJson();
        var_dump($extension, $json);
        return new Message($json, $extension, $filePath);
    }

    static function generateErrorMessage(Exception $e): ErrorMessage
    {
        $suggestion = match ($e->getCode()) {
            20 => '指定のファイルと処理内容を確認して改めてご利用ください。',
            30 => 'ファイルへの処理内容を確認して改めてご利用ください。',
            default => '時間をおいて改めてご利用ください。'
        };

        $error = [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'suggestion' => $suggestion
        ];
        return new ErrorMessage(json_encode($error), '', '');
    }
}