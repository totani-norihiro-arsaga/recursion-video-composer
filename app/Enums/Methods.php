<?php

declare(strict_types=1);

namespace App\Enums;

use App\Rpcs\ChangeRateFactory;
use App\Rpcs\ChangeResolutionFactory;
use App\Rpcs\CompressProcessFactory;
use App\Rpcs\ConvertToAudioFactory;
use App\Rpcs\GenerateGifFactory;
use App\Rpcs\RpcFactory;
use App\FFmpegs\FFmpeg;

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
            $choices .= sprintf("%s:%d\n", $case->description(), $case->value);
        }
        return $choices;
    }

    public function getRcpData(): RpcFactory
    {
        var_dump($this);
        return match ($this) {
            self::Compress => new CompressProcessFactory(),
            self::ChangeResolution => new ChangeResolutionFactory(),
            self::ChangeRate => new ChangeRateFactory(),
            self::ConvertToAudio => new ConvertToAudioFactory(),
            self::GenerateGif => new GenerateGifFactory(),
        };
    }

    public function getProcedure() {
        return match ($this) {
            self::Compress => fn($inputFilePath) => FFmpeg::compress($inputFilePath),
            self::ChangeResolution => fn($inputFilePath, $arguments) => FFmpeg::changeResolution($inputFilePath, ...$arguments),
            self::ChangeRate => fn($inputFilePath, $arguments) => FFmpeg::changeRate($inputFilePath, ...$arguments),
            self::ConvertToAudio => fn($inputFilePath, $arguments) => FFmpeg::convertToAudio($inputFilePath),
            self::GenerateGif => fn($inputFilePath, $arguments) => FFmpeg::generateGif($inputFilePath, ...$arguments),
        };
    }
}
