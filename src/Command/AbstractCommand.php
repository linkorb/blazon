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

}
