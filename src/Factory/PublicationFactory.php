<?php

declare(strict_types=1);

namespace Blazon\Factory;

use Blazon\Model\Publication;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Blazon\Plugin\BaseServicePlugin;
use Blazon\Event;
use Blazon\Dispatcher;
use RuntimeException;

class PublicationFactory
{
    public static function build(ContainerInterface $container, string $sourcePath, array $config): Publication
    {
        // $blazon = $container->get(Blazon::class);

        if (!file_exists($sourcePath . '/blazon.yaml')) {
            throw new RuntimeException('Invalid blazon publication path (blazon.yaml not found');
        }

        $logger = $container->get(LoggerInterface::class);
        $dispatcher = $container->get(Dispatcher::class);
        $publication = new Publication($sourcePath);

        $filenames = glob($sourcePath . '/plugins/*.php');
        foreach ($filenames as $filename) {
            $name = realpath($filename);
            $name = str_replace($sourcePath, '', $name);
            $logger->debug('Auto loading ' . $name);
            require_once($filename);
        }


        // Initialize Service Plugins
        foreach ($config['plugins'] ?? [] as $pluginName => $pluginConfig) {
            if (is_subclass_of($pluginName, BaseServicePlugin::class)) {
                $logger->debug('Registering service plugin: ' . $pluginName);
                $plugin = $container->get($pluginName);
                $publication->addPlugin($plugin);

                $subscribedEvents = $plugin->getSubscribedEvents();
                foreach ($subscribedEvents as $eventName=>$eventHandler) {
                    $dispatcher->subscribe($eventName, [$plugin, $eventHandler]);
                }
            }
        }

        $event = new Event\ContainerBuildEvent($container);
        $dispatcher->dispatch($event);

        // Initialize Non-Service Plugins
        foreach ($config['plugins'] ?? [] as $pluginName => $pluginConfig) {
            if (!is_subclass_of($pluginName, BaseServicePlugin::class)) {
                $logger->debug('Registering plugin: ' . $pluginName);
                $plugin = $container->get($pluginName);
                $publication->addPlugin($plugin);

                $subscribedEvents = $plugin->getSubscribedEvents();
                foreach ($subscribedEvents as $eventName=>$eventHandler) {
                    $dispatcher->subscribe($eventName, [$plugin, $eventHandler]);
                }
            }
        }

        $event = new Event\PluginInitEvent();
        $dispatcher->dispatch($event);

        $event = new Event\PublicationInitEvent($publication);
        $dispatcher->dispatch($event);

        $event = new Event\PublicationReadyEvent($publication);
        $dispatcher->dispatch($event);

        return $publication;
    }
}
