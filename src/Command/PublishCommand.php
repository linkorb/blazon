<?php

namespace Blazon\Command;

use RuntimeException;

use Blazon\Publisher\StaticHtmlPublisher;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;

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
        $this->logger = new ConsoleLogger($output);

        $publication = $this->buildPublication($input);


        $publisher = new StaticHtmlPublisher($this->logger, $this->destinationPath);
        $publisher->publish($publication);

        $output->writeLn('The end');
        return 0;
    }
}
