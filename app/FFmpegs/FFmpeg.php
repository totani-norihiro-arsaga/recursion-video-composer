<?php

declare(strict_types=1);

class FFmpeg
{
    private const OUTPUT_DIR = __DIR__.'/../../storage/';

    public static function compress($inputFilePath, $mediaType): string
    {
        $outputFilePath = self::makeOutPutPath($mediaType);
        if(! exec("ffmpeg -i {$inputFilePath} -crf 20 {$outputFilePath}")) {
            throw new Exception('圧縮に失敗しました。');
        }
        return $outputFilePath;
    }
    public static function changeResolution($inputFilePath, $mediaType, $width, $height) {
        $outputFilePath = self::makeOutPutPath($mediaType);
        if(! exec("ffmpeg -i {$inputFilePath} -s {$width}x{$height} {$outputFilePath}")) {
            throw new Exception('圧縮に失敗しました。');
        }
        return $outputFilePath;
    }
//    public static function changeRate() {}
//    public static function convertToAudio() {}
//    public static function generateGif() {}

    private static function makeOutPutPath($mediaType) {
        return sprintf('%s%s.%s', self::OUTPUT_DIR,date('d-m-Y-H-i-s'),$mediaType);
    }
}