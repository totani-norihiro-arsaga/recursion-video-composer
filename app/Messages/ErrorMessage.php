<?php

declare(strict_types=1);

namespace App\Messages;

class ErrorMessage extends Message
{
    private int $code;
    private string $message;
    private string $suggestion;
    public function __construct(string $json, string $mediaType, string $filePath)
    {
        parent::__construct($json, $mediaType, $filePath);
        if($mediaType || $filePath) {
            throw new \Exception('エラーメッセージとしてインスタンスできません。');
        }

        $content = json_decode($json, true);
        if(is_null($content['code']) || is_null($content['message']) || is_null($content['suggestion'])) {
            throw new \Exception('エラーメッセージとしてインスタンスできません。');
        }
        $this->code = $content['code'];
        $this->message = $content['error'];
        $this->suggestion = $content['suggestion'];
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getSuggestion(): string
    {
        return $this->suggestion;
    }
}