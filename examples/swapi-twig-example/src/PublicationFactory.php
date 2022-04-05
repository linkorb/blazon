<?php

use Blazon\Model\Publication;
use Blazon\Model\Document;
use Blazon\GraphQL\GraphQLClient;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class PublicationFactory
{
    public static function build(ContainerInterface $container, string $sourcePath, array $config): Publication
    {
        $publication = new Publication($sourcePath);

        // Initialize graphql client
        $url = 'https://swapi-graphql.netlify.app/.netlify/functions/index';

        $client = GraphQLClient::buildFromUrl($url);
        $client->setQueryPath($publication->getPath() . '/graphql');

        // Initialize twig renderer
        $loader = new \Twig\Loader\FilesystemLoader();
        $loader->addPath($publication->getPath() . '/templates');
        $twig = new \Twig\Environment($loader, []);

        // Query all films
        $data = $client->queryByName('films');
        $films = $data['allFilms']['films'] ?? [];

        // Register a document for the film index
        $document = new Document(
            '', // no path, i.e. root
            function() use ($films, $twig) {
                $html = $twig->render('index.html.twig', ['films' => $films]);
                return $html;
            }
        );
        $publication->addDocument($document);

        // Register a document for each film
        foreach ($films as $node) {
            $document = new Document(
                'episode-' . $node['episodeID'],
                function() use ($node, $twig) {
                    $html = $twig->render('film.html.twig', ['film' => $node]);
                    return $html;
                }
            );
            $publication->addDocument($document);
        }


        // Query all characters
        $data = $client->queryByName('characters');
        $characters = [];
        foreach ($data['allPeople']['edges'] as $edge) {
            $characters[] = $edge['node'];
        }

        foreach ($characters as $node) {
            $document = new Document(
                'character-' . $node['id'],
                function() use ($node, $twig) {
                    $html = $twig->render('character.html.twig', ['character' => $node]);
                    return $html;
                }
            );
            $publication->addDocument($document);
        }

        return $publication;
    }
}
