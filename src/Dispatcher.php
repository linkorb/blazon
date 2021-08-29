<?php

namespace Blazon;

use League\Event\EventDispatcher as LeagueDispatcher;
use Psr\Log\LoggerInterface;

class Dispatcher
{
    public function __construct(LoggerInterface $logger, LeagueDispatcher $dispatcher)
    {
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    public function subscribe(string $eventName, callable $callable)
    {
        $this->dispatcher->subscribeTo($eventName, $callable);
    }

    public function dispatch(object $event)
    {
        $this->logger->info("Dispatching event: " . get_class($event));
        $this->dispatcher->dispatch($event);
    }
}
