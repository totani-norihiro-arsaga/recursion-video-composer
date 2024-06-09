<?php

declare(strict_types=1);

namespace App\Messages;

class Message
{
    /**
     * @param  string  $json
     * @param  string  $mediaType
     * @param  string  $filePath
     */
    public function __construct(private string $json, private string $mediaType, private string $filePath)
    {
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }

    /**
     * @return string
     */
    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }
}