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
            ->addOption(
                'watch',
                'w',
                InputOption::VALUE_NONE,
                'Watch and auto re-publish'
            )
        ;
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger = new ConsoleLogger($output);

        $watch = $input->getOption('watch');


        do {
            $publication = $this->buildPublication($input);
            $hash = $this->getContentHash($publication);

            $this->logger->info("Content hash: " . $hash);

            $publisher = new StaticHtmlPublisher($this->logger, $this->destinationPath);
            $publisher->publish($publication);

            if ($watch) {
                $this->logger->info("Watching for changes: " . date('Y-m-d H:i:s') . ' (press CTRL+C to exit)');
                do {
                    sleep(1);
                    $newHash = $this->getContentHash($publication);
                } while ($newHash == $hash);
                $hash = $newHash;
            }
        } while ($watch);

        return 0;
    }
}
