<?php

namespace Blazon\Event;

use Psr\Container\ContainerInterface;

/**
 * Generic container event
 */
abstract class ContainerEvent
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
