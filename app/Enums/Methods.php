<?php

declare(strict_types=1);

namespace App\Enums;

use App\Rpcs\ChangeResolutionFactory;
use App\Rpcs\CompressProcessFactory;
use App\Rpcs\RpcFactory;
use FFmpeg;

enum Methods:int
{
    case Compress = 1;
    case ChangeResolution = 2;
    case ChangeRate = 3;
    case ConvertToAudio = 4;
    case GenerateGif = 5;

    public function description() {
        return match ($this) {
            self::Compress => '圧縮する',
            self::ChangeResolution => '解像度を変える',
            self::ChangeRate => 'アスペクト比を変える',
            self::ConvertToAudio => '音声データへ変換する',
            self::GenerateGif => '指定の時間幅でGIFを作成する',
        };
    }

    public static function getChoices(): string
    {
        $cases = self::cases();
        $choices = '';
        foreach ($cases as $case) {
            $choices .= printf("%s:%d\n", $case->description(), $case->value);
        }
        return $choices;
    }

    public function getRcpData(): RpcFactory
    {
        return match ($this) {
            self::Compress => new CompressProcessFactory(),
            self::ChangeResolution => new ChangeResolutionFactory(),
        };
    }

    public function getProcedure() {
        return match ($this) {
            self::Compress => fn($inputFilePath, $mediaType) => FFmpeg::compress($inputFilePath, $mediaType),
            self::ChangeResolution => fn($inputFilePath, $mediaType, $arguments) => FFmpeg::changeResolution($inputFilePath, $mediaType, ...$arguments),
        };
    }
}
