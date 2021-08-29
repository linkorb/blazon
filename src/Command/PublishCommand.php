<?php

namespace Blazon\Command;

use RuntimeException;

use Blazon\Blazon;
use Blazon\Model\Document;
use Blazon\Model\Publication;
use Blazon\Publisher\StaticHtmlPublisher;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Yaml\Yaml;
use Blazon\Factory\ContainerFactory;
use Blazon\Factory\PublicationFactory;
use Blazon\Event;
use Blazon\Dispatcher;

class PublishCommand extends AbstractCommand
{
    public function configure()
    {
        $this->setName('publish')
            ->setDescription('Publish')
            // ->addArgument(
            //     'target',
            //     InputArgument::REQUIRED,
            //     'Name of the xillion config file'
            // )
            ->addOption(
                'source',
                's',
                InputOption::VALUE_REQUIRED,
                'Source directory, containing the blazon.yaml file'
            )
            ->addOption(
                'destination',
                'd',
                InputOption::VALUE_REQUIRED,
                'Destination directory'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $publicationPath = __DIR__ . '/../../examples/test-site';

        $sourcePath = $input->getOption('source');

        if (!$sourcePath) {
            $sourcePath = getcwd();
        }

        if (substr($sourcePath,0,1)!='/') {
            $sourcePath = getcwd() . '/' . $sourcePath;
        }

        if (!file_exists($sourcePath . '/blazon.yaml')) {
            throw new RuntimeException('Invalid blazon publication path (blazon.yaml not found): ' . $sourcePath);
        }

        $destinationPath = $input->getOption('destination');

        if (!$destinationPath) {
            $destinationPath = $sourcePath . '/build';
        }


        $logger = new ConsoleLogger($output);

        $config = file_get_contents($sourcePath . '/blazon.yaml');
        $config = Yaml::parse($config);

        $container = ContainerFactory::build($config, $logger);
        $publication = PublicationFactory::build($container, $sourcePath, $config);


        $publisher = new StaticHtmlPublisher($logger, $destinationPath);
        $publisher->publish($publication);

        $output->writeLn('The end');
        return 0;
    }
}
