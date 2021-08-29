<?php

namespace Blazon\Model;

class Document
{
    private $path = null;

    public function __construct(string $path, $handler)
    {
        $this->path = $path;
        $this->handler = $handler;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHandler()
    {
        return $this->handler;
    }
}
