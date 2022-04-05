<?php

namespace Blazon\Publisher;

use Blazon\Model\Publication;
use Psr\Log\LoggerInterface;

class StaticHtmlPublisher
{
    private $outputPath;
    public function __construct(LoggerInterface $logger, string $outputPath)
    {
        $this->logger = $logger;
        $this->outputPath = $outputPath;
    }

    public function publish(Publication $publication)
    {
        $this->logger->info('Publishing!');
        foreach ($publication->getDocuments() as $document) {

            $this->logger->info('Generating `' . $document->getPath() . '`');

            $handler = $document->getHandler();
            $content = null;
            if (is_callable($handler)) {
                $content = $handler();
            }
            if (is_string($handler)) {
                $content = $handler;
            }
            $path = $this->outputPath . '/' . $document->getPath();

            $path .= '/index.html';

            $filename = basename($path);
            $dirname = dirname($path);
            if (!file_exists($dirname)) {
                mkdir($dirname, 0777, true);
            }

            // echo $dirname . '::' . $filename . ':::' . $content . PHP_EOL;

            file_put_contents($path, $content);
        }


    }
}
