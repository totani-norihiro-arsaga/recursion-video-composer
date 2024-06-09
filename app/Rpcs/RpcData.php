<?php

declare(strict_types=1);

namespace App\Rpcs;

use JsonSerializable;

class RpcData implements JsonSerializable
{
    public function __construct(private int $methodNumber, private array $arguments) {
    }

    public function getJson(): false|string
    {
        return json_encode($this);
    }

    public function jsonSerialize(): array
    {
        return ['method' => $this->methodNumber, 'arguments' => $this->arguments];
    }

    /**
     * @return int
     */
    public function getMethodNumber(): int
    {
        return $this->methodNumber;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }


}