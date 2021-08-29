<?php

declare(strict_types=1);

namespace Blazon\Factory;

use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Container\ContainerInterface;
use Blazon\Blazon;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
// use Blazon\Session\SessionInterface;
use Psr\SimpleCache\CacheInterface;
use Redis;
use League\Event\EventDispatcher;
use Blazon\Dispatcher;

class ContainerFactory
{
    public static function build(array $config, LoggerInterface $logger): ContainerInterface
    {
        // add records to the log
        $logger->info('Building container');

        $container = new Container();
        $container->defaultToShared();

        // Activate auto-wiring, with cached resolutions
        $container->delegate(
            (new ReflectionContainer(true))
        );

        // alias container to psr container interface
        $container->add(ContainerInterface::class, $container);

        // Register logger
        $container->add(LoggerInterface::class, $logger);

        // ExpressionLanguage
        // $container->add(ExpressionLanguage::class, function() use ($container) {
        //     $expressionLanguage = new ExpressionLanguage();
        //     return $expressionLanguage;
        // });

        return $container;
    }
}
