<?php

namespace Blazon\Plugin;

use Xillion\Core\ResourceContext\ResourceContext;
use Xillion\Core\ResourceRepository\ArrayResourceRepository;
use Xillion\Core\Resource\ResourceLoader;
use Blazon\Plugin\BaseServicePlugin;
use Psr\Log\LoggerInterface;
use Blazon\Dispatcher;
use Blazon\Event;

class XillionPlugin extends BaseServicePlugin
{
    protected $context;
    protected $arrayRepository;
    protected $loader;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function getContext(): ResourceContext
    {
        return $this->context;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Event\ContainerBuildEvent::class => 'onContainerBuild',
        ];
    }

    public function load(array $config)
    {
        $this->loader->load($this->arrayRepository, $config);
    }

    public function onContainerBuild(Event\ContainerBuildEvent $event): void
    {
        $container = $event->getContainer();
        $dispatcher = $this->dispatcher;

        $this->context = new ResourceContext();
        $this->arrayRepository = new ArrayResourceRepository($this->context);
        $this->context->addRepository($this->arrayRepository);

        $this->loader = new ResourceLoader();

        // $event = new XillionInitEvent($context);
        // $dispatcher->dispatch($event);


        $container->add(ResourceContext::class, $this->context);
    }


}
