<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;
use Blazon\Factory\ContainerFactory;
use Blazon\Factory\PublicationFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


require_once __DIR__.'/../vendor/autoload.php';

if (!file_exists(dirname(__DIR__) . '/.env')) {
    exit('Please copy .env.dist to .env, and adjust the settings.');
}
$dotenv = new Dotenv();
$dotenv->usePutenv();
$dotenv->load(dirname(__DIR__) . '/.env');

if (getenv('DEBUG')=='true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$logger = new Logger('blazon');
$logger->pushHandler(new StreamHandler('/tmp/blazon.log', Logger::DEBUG));

$sourcePath = getenv('SOURCE_PATH');

if (!$sourcePath) {
    throw new RuntimeException('SOURCE_PATH is unconfigured, but required');
}

if (!file_exists($sourcePath . '/blazon.yaml')) {
    throw new RuntimeException('Invalid blazon source path (blazon.yaml not found): ' . $sourcePath);
}


// ==============
// TODO: reuse AbstractCommand's factory
// ==============

$config = file_get_contents($sourcePath . '/blazon.yaml');
$config = Yaml::parse($config);

$container = ContainerFactory::build($config, $logger);
$factoryFilename = $sourcePath . '/src/PublicationFactory.php';
if (file_exists($factoryFilename)) {
    $logger->info("Loading custom PublicationFactory file: " . $factoryFilename);

    require_once($factoryFilename);
    $factoryClassName = 'PublicationFactory';
    if (!class_exists($factoryClassName)) {
        $logger->error("Invalid PublicationFactory.php file, does not define class `" . $factoryClassName . "`");
        throw new RuntimeException("Failed to build publication");
    }
    $publication = $factoryClassName::build($container, $sourcePath, $config);
} else {
    $logger->info("Using default PublicationFactory. No custom factory found: " . $factoryFilename);
    $publication = PublicationFactory::build($container, $sourcePath, $config);
}


// ==============
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$uri = $request->getUri();
$path = $uri->getPath();

// $documents = $publication->getDocuments();
// foreach ($documents as $document) {
//     echo $document->getPath();
// }

$document = $publication->getDocument($path);

if ($document) {
    $handler = $document->getHandler();
    $content = null;
    if (is_callable($handler)) {
        $content = $handler();
    }
    if (is_string($handler)) {
        $content = $handler;
    }

    header('Content-type: text/html');
    echo $content;
    exit(0);
}

// check if public file

$filename = $sourcePath . '/public' . $path;
if (file_exists($filename)) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $map = [
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'css' => 'text/css',
        'js' => 'text/javascript',
        'text' => 'text/plain',
        'html' => 'text/html',
    ];
    $contentType = $map[$ext] ?? 'application/octet-stream';
    header('Content-type: ' . $contentType);

    // Instruct browser to cache for 24 hours
    header('Pragma: public');
    header('Cache-Control: max-age=86400');
    header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));

    $content = file_get_contents($filename);
    echo $content;
    exit();
}

throw new RuntimeException("404 Not found");


