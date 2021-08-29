<?php

namespace Blazon\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


abstract class AbstractCommand extends Command
{
    protected $publisher;

    public function __construct($publisher)
    {
        $this->publisher = $publisher;
        parent::__construct();
    }
}
