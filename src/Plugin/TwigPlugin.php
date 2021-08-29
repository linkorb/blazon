<?php

namespace Blazon\Plugin;

use Blazon\Plugin\BaseServicePlugin;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Psr\Container\ContainerInterface;
use Blazon\Dispatcher;
use Blazon\Event;

class TwigPlugin extends BaseServicePlugin
{
    protected $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Event\ContainerBuildEvent::class => 'onContainerBuild',
        ];
    }

    public function onContainerBuild(Event\ContainerBuildEvent $event): void
    {
        $container = $event->getContainer();
        $dispatcher = $this->dispatcher;

        $container->add(Environment::class, function() use ($dispatcher) {
            $loader = new \Twig\Loader\FilesystemLoader();
            $twig = new \Twig\Environment($loader, []);

            $event = new TwigInitEvent($twig);
            $dispatcher->dispatch($event);
            return $twig;
        });
    }


}
