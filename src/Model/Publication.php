<?php

namespace Blazon\Model;


class Publication
{
    private $path;
    private $documents = [];
    private $plugins = [];

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function addDocument($document)
    {
        $this->documents[$document->getPath()] = $document;
    }

    public function getDocuments(): array
    {
        return $this->documents;
    }

    public function addPlugin($plugin)
    {
        $this->plugins[$plugin->getName()] = $plugin;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }
}
