<?php

declare(strict_types=1);

namespace App\FFmpegs;

use DateTimeImmutable;
use Exception;

class FFmpeg
{
    private const OUTPUT_DIR = __DIR__.'/../../storage/tmp/';

    public static function compress($inputFilePath): string
    {
        $inputFileExtension = pathinfo($inputFilePath, PATHINFO_EXTENSION);
        $outputFilePath = self::makeOutPutPath($inputFileExtension);
        $outputLines = [];
        $resultCode = 0;
        exec("ffmpeg -i {$inputFilePath} -crf 20 {$outputFilePath}", $outputLines, $resultCode);
        if(!$resultCode === 0) {
            throw new Exception('圧縮に失敗しました。', 30);
        }
        return $outputFilePath;
    }
    public static function changeResolution($inputFilePath, $width, $height): string
    {
        $inputFileExtension = pathinfo($inputFilePath, PATHINFO_EXTENSION);
        $outputFilePath = self::makeOutPutPath($inputFileExtension);
        $outputLines = [];
        $resultCode = 0;
        exec("ffmpeg -i {$inputFilePath} -s {$width}x{$height} {$outputFilePath}", $outputLines, $resultCode);
        if(! $resultCode === 0) {
            throw new Exception('解像度の変更に失敗しました。', 30);
        }
        return $outputFilePath;
    }
    public static function changeRate($inputFilePath, $numerator, $denominator): string
    {
        $inputFileExtension = pathinfo($inputFilePath, PATHINFO_EXTENSION);
        $outputFilePath = self::makeOutPutPath($inputFileExtension);
        $outputLines = [];
        $resultCode = 0;
        exec("ffmpeg -i {$inputFilePath} -aspect {$numerator}:{$denominator} {$outputFilePath}", $outputLines, $resultCode);
        if(! $resultCode === 0) {
            throw new Exception('アスペクト比の変更に失敗しました。', 30);
        }
        return $outputFilePath;
    }
    public static function convertToAudio($inputFilePath): string
    {
        $outputFilePath = self::makeOutPutPath('mp3');
        $outputLines = [];
        $resultCode = 0;
        exec("ffmpeg -i {$inputFilePath} {$outputFilePath}", $outputLines, $resultCode);
        if(! $resultCode === 0) {
            throw new Exception('解像度の変更に失敗しました。', 30);
        }
        return $outputFilePath;
    }
    public static function generateGif($inputFilePath, $start, $duration): string
    {
        $outputFilePath = self::makeOutPutPath('gif');
        $outputLines = [];
        $resultCode = 0;
        exec("ffmpeg -i {$inputFilePath} -ss {$start} -t {$duration} -vf \"fps=10,scale=320:-1:flags=lanczos\" {$outputFilePath}", $outputLines, $resultCode);
        if(! $resultCode === 0) {
            throw new Exception('gifの生成に失敗しました。', 30);
        }
        return $outputFilePath;
    }

    private static function makeOutPutPath($mediaType): string
    {
        return sprintf('%s%s.%s', self::OUTPUT_DIR,(new DateTimeImmutable())->format('Y-m-d-H-i-s-u'),$mediaType);
    }
}