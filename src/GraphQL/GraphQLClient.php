<?php

namespace Blazon\GraphQL;

use GuzzleHttp\Client as GuzzleClient;
use RuntimeException;

class GraphQLClient
{
    protected $url;

    public static function buildFromUrl(string $url): self
    {
        $client = new self();
        $client->url = $url;
        return $client;
    }

    public function setQueryPath(string $path)
    {
        $this->queryPath = $path;
    }

    public function queryByName(string $name, array $variables = []): array
    {
        $queryFilename = $this->queryPath . '/' . $name . '.graphql';
        if (!file_exists($queryFilename)) {
            throw new RuntimeException("Query not found: " . $name);
        }

        $query = file_get_contents($queryFilename);

        $response = (new GuzzleClient)->request('post', $this->url, [
            'headers' => [
                // 'Authorization' => 'bearer ' . $token,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'query' => $query
            ]
        ]);

        // $response = (new GuzzleClient)->request('get', $this->url . '/?query=' . urlencode($query), [
        //     'headers' => [
        //         // 'Authorization' => 'bearer ' . $token,
        //         'Content-Type' => 'application/json'
        //     ],
        //     // 'json' => [
        //     //     'query' => $query
        //     // ]
        // ]);

        $json = $response->getBody()->getContents();
        echo $json;
        $data = json_decode($json, true);
        $data = $data['data'];
        // print_r($data);
        return $data;
    }

}
