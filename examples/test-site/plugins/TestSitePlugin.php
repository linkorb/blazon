<?php

use Symfony\Component\Yaml\Yaml;
use Blazon\Model\Document;
use Blazon\Plugin\BasePlugin;
use Blazon\Event;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Psr\Container\ContainerInterface;
use Blazon\Plugin\XillionPlugin;

class TestSitePlugin extends BasePlugin
{
    public function __construct(Environment $twig, XillionPlugin $xillionPlugin,  LoggerInterface $logger)
    {
        $this->twig = $twig;
        $this->xillionPlugin = $xillionPlugin;
        // $this->context = $context;
        $this->logger = $logger;

        $loader = $twig->getLoader();
        $loader->addPath(__DIR__ . '/../templates');
    }

    public function getSubscribedEvents(): array
    {
        return [
            Event\PublicationInitEvent::class => 'onInit',
            Event\PublicationReadyEvent::class => 'onReady',
            // Event\XillionInitEvent::class => 'onXillionInit',
        ];
    }

    // public function onXillionInit(XillionEvent $event)
    // {
    //     // $context = $event->getContext();
        // $yaml = file_get_contents(__DIR__ . '/../content/hello.yaml');
        // $config = Yaml::parse($yaml);
    //     // $loader->load($arrayRepository, $config);

    //     $this->xillionPlugin->load($config);

    // }

    public function onInit(Event\PublicationEvent $event): void
    {
        $publication = $event->getPublication();
        $context = $this->xillionPlugin->getContext();

        $yaml = file_get_contents(__DIR__ . '/../content/hello.yaml');
        $config = Yaml::parse($yaml);
        $this->xillionPlugin->load($config);


        $document = new Document(
            '/',
            function() {
                return 'The index!';
            }
        );

        $publication->addDocument($document);

        $resources = $context->getResourcesByAttribute(
            'core.xillion.cloud/profiles',
            'content.xillion.cloud/profiles/document'
        );
        foreach ($resources as $resource) {
            echo "* " . $resource['content.xillion.cloud/title'] . PHP_EOL;

            $document = new Document(
                $resource['content.xillion.cloud/path'],
                function() use ($resource) {
                    return $this->resourceRenderer($resource);
                    // $template = 'layout.html.twig';
                    // $data = [
                    //     'resource' => $resource,
                    // ];
                    // return $this->twig->render($template, $data);
                }
            );
            $publication->addDocument($document);
        }
    }

    public function resourceRenderer($resource)
    {
        $template = 'layout.html.twig';
        $data = [
            'resource' => $resource,
        ];
        return $this->twig->render($template, $data);
    }

    public function onReady(Event\PublicationEvent $event): void
    {
        echo "Ready event :P\n";
        echo $this->twig->render('layout.html.twig', []);
    }
}
