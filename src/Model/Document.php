<?php

namespace Blazon\Model;

use InvalidArgumentException;

class Document
{
    private $path = null;

    public function __construct(string $path, $handler, array $data = [])
    {
        if ($path[0]!='/') {
            throw new InvalidArgumentException("Invalid document path. Should start with a slash: " . $path);
        }
        if ((substr($path, -1, 1)=='/') && ($path != '/')) {
            throw new InvalidArgumentException("Invalid document path. No trailing slash allowed: " . $path);
        }
        $this->path = $path;
        $this->handler = $handler;
        $this->data = $data;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
