<?php

namespace Blazon\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Yaml\Yaml;
use Blazon\Blazon;
use Blazon\Model\Document;
use Blazon\Model\Publication;
use Blazon\Factory\ContainerFactory;
use Blazon\Factory\PublicationFactory;
use Blazon\Event;
use Blazon\Dispatcher;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;


abstract class AbstractCommand extends Command
{
    protected $logger;
    protected $sourcePath;
    protected $destinationPath;

    protected function buildPublication(InputInterface $input): Publication
    {
        $this->sourcePath = $input->getOption('source');

        if (!$this->sourcePath) {
            $this->sourcePath = getcwd();
        }

        if (substr($this->sourcePath, 0, 1)!='/') {
            $this->sourcePath = getcwd() . '/' . $this->sourcePath;
        }

        if (!file_exists($this->sourcePath . '/blazon.yaml')) {
            throw new RuntimeException('Invalid blazon publication path (blazon.yaml not found): ' . $this->sourcePath);
        }

        $this->destinationPath = $input->getOption('destination');

        if (!$this->destinationPath) {
            $this->destinationPath = $this->sourcePath . '/build';
        }

        $config = file_get_contents($this->sourcePath . '/blazon.yaml');
        $config = Yaml::parse($config);

        $container = ContainerFactory::build($config, $this->logger);
        $factoryFilename = $this->sourcePath . '/src/PublicationFactory.php';
        if (file_exists($factoryFilename)) {
            $this->logger->info("Loading custom PublicationFactory file: " . $factoryFilename);

            require_once($factoryFilename);
            $factoryClassName = 'PublicationFactory';
            if (!class_exists($factoryClassName)) {
                $this->logger->error("Invalid PublicationFactory.php file, does not define class `" . $factoryClassName . "`");
                throw new RuntimeException("Failed to build publication");
            }
            $publication = $factoryClassName::build($container, $this->sourcePath, $config);
        } else {
            $this->logger->info("Using default PublicationFactory. No custom factory found: " . $factoryFilename);
            $publication = PublicationFactory::build($container, $this->sourcePath, $config);
        }

        return $publication;
    }



    protected function getContentHash($publication): string
    {

        $path = $publication->getPath();

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        $filenames = array();

        foreach ($rii as $file) {

            if ($file->isDir()){
                continue;
            }

            $include = true;
            $filename = $file->getPathname();
            $filename = substr($filename, strlen($publication->getPath())+1);

            // TODO: base on .gitignore?
            if (substr($filename, 0, 6) == 'build/') {
                $include = false;
            }
            if (substr($filename, 0, 5) == '.git/') {
                $include = false;
            }
            if (substr($filename, 0, 7) == 'vendor/') {
                $include = false;
            }
            if (substr($filename, 0, 13) == 'node_modules/') {
                $include = false;
            }

            if (substr(basename($filename), 0, 1) == '.') {
                $include = false;
            }

            if ($include) {
                $filenames[] = $filename;
            }
        }

        // print_r($filenames);
        $hash = null;
        foreach ($filenames as $filename) {
            $content = file_get_contents($publication->getPath() .'/' . $filename);
            $hash = sha1($content . $hash);
        }
        return $hash;
    }


}
